<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Data_Paginator;
use Jet\Data_Text;
use Jet\Http_Request;
use Jet\Locale;
use Jet\Tr;
use Jet\UI_dataGrid;
use JetApplication\EShop;
use JetApplication\EShops;

class Report_General_Search extends Report_General
{
	public const KEY = 'search';
	protected ?string $title = 'Search';
	protected bool $one_eshop_mode = true;
	protected bool $is_default = false;
	
	protected EShop $eshop;
	
	protected array $sub_reports = [
		'summary' => 'Summary',
	];
	
	public function prepare_summary() : void
	{
		$this->eshop = EShops::get( $this->getSelectedEshopKeys()[0] );
		
		
		$data = $this->getRawData();
		
		$grid = new UI_dataGrid();
		$grid->setSortUrlCreator( function( $column_name, $desc ) {
			$column_name = $desc ? '-'.$column_name : $column_name;
			
			return Http_Request::currentURI( set_GET_params: ['sort'=>$column_name] );
		} );
		
		$grid->setPaginator( new Data_Paginator(
			current_page_no: Http_Request::GET()->getInt('p'),
			items_per_page: 50,
			URL_creator: function( int $p ) {
				return Http_Request::currentURI( set_GET_params: ['p'=>$p] );
			}
		) );
		
		
		$grid->addColumn('search_query', Tr::_('Search query') )
			->setRenderer(function( array $d ) {
				echo Data_Text::htmlSpecialChars( $d['search_query']??'' );
			});
		
		$grid->addColumn('search_count', Tr::_('Count of search sessions') )
			->setRenderer(function( array $d ) {
				echo Locale::int( $d['search_count'] );
			});
		
		$grid->addColumn('found_something', Tr::_('Found sometning') )
			->setRenderer(function( array $d ) {
				echo Tr::_( $d['found_something']?'Yes':'No', dictionary: Tr::COMMON_DICTIONARY );
			});
			
		$grid->addColumn('count_products', Tr::_('Count - products') )
			->setRenderer(function( array $d ) {
				echo Locale::int( $d['count_products'] );
			});
			
		$grid->addColumn('count_categories', Tr::_('Count - categories') )
			->setRenderer(function( array $d ) {
				echo Locale::int( $d['count_categories'] );
			});
			
		$grid->addColumn('count_articles', Tr::_('Count - articles') )
			->setRenderer(function( array $d ) {
				echo Locale::int( $d['count_articles'] );
			});
		
		$sort_by = Http_Request::GET()->getString('sort', default_value: '-search_count', valid_values: [
			'search_query',
			'-search_query',
			'search_count',
			'-search_count',
			'found_something',
			'-found_something',
			'count_products',
			'-count_products',
			'count_categories',
			'-count_categories',
			'count_articles',
			'-count_articles'
		]);
		
		$grid->setSortBy( $sort_by );
		
		if( $sort_by[0]=='-' ) {
			$sort_by = substr($sort_by, 1);
			
			uasort( $data, function( array $a, array $b ) use ($sort_by) {
				$a_v = $a[$sort_by];
				$b_v = $b[$sort_by];
				
				return $b_v<=>$a_v;
			} );
			
		} else {
			uasort( $data, function( array $a, array $b ) use ($sort_by) {
				$a_v = $a[$sort_by];
				$b_v = $b[$sort_by];
				
				return $a_v<=>$b_v;
			} );
		}
		
		$grid->setData( $data );
		
		
		
		$this->view->setVar('grid', $grid);
	}
	
	
	
	
	
	protected function getRawData() : array
	{
		$s_page = Event_Search::dataFetchAll(
			select: [
				'search_query',
				'found_something',
				'result_ids',
			],
			where: [
				$this->eshop->getWhere(),
				'AND',
				[
					'date_time >=' => $this->date_from,
					'AND',
					'date_time <=' => $this->date_to,
				]
			]
		);
		
		$s_whisperer = Event_SearchWhisperer::dataFetchAll(
			select: [
				'search_query',
				'found_something',
				'result_ids'
			],
			where: [
				$this->eshop->getWhere(),
				'AND',
				[
					'date_time >=' => $this->date_from,
					'AND',
					'date_time <=' => $this->date_to,
				]
			]
		);
		
		$result = [];
		
		$collectData = function( $searchs ) use (&$result) {
			foreach($searchs as $s) {
				$search_query = trim(mb_strtolower( $s['search_query'] ));
				if(!$search_query) {
					continue;
				}
				$found_something = $s['found_something'];
				$result_ids = $s['result_ids'];
				
				if(!isset($result[$search_query])) {
					$result[$search_query] = [
						'search_query' => $search_query,
						'search_count' => 0,
						'found_something' => false,
						'count_products' => 0,
						'count_categories' => 0,
						'count_articles' => 0
					];
				}
				
				$result[$search_query]['search_count']++;
				
				if($found_something) {
					$result[$search_query]['found_something'] = true;
					
					foreach($result_ids as $k=>$ids) {
						$result[$search_query]['count_'.$k] += count($ids);
					}
				}
			}
		};
		
		$collectData( $s_page );
		$collectData( $s_whisperer );
		
		foreach($result as $qs=>$d) {
			$result[$qs]['count_products'] = round($d['count_products']/$d['search_count']);
			$result[$qs]['count_categories'] = round($d['count_categories']/$d['search_count']);
			$result[$qs]['count_articles'] = round($d['count_articles']/$d['search_count']);
		}
		
		return $result;
	}
	
}