<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Tr;
use Jet\UI;
use Jet\UI_badge;
use JetApplication\Application_Service_Admin;
use JetApplication\ProductListing;

#[DataModel_Definition(
	name: 'ja_event_search',
	database_table_name: 'ja_event_search',
)]
class Event_Search extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $search_query = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $found_something = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected array $result_ids = [];
	
	protected ?Event_ProductsListView $product_list_view = null;
	
	
	public function cancelDefaultEvent(): bool
	{
		return true;
	}

	public function init( string $q, array $result_ids, ?ProductListing $product_listing=null ) : void
	{
		$this->search_query = $q;
		$this->found_something = count($result_ids);
		$this->result_ids = $result_ids;
		if($product_listing) {
			$this->product_list_view = new Event_ProductsListView();
			$this->product_list_view->init( $product_listing );
		}
		
	}
	
	public function afterAdd() : void
	{
		parent::afterAdd();
		
		if( $this->product_list_view ) {
			$this->product_list_view->setEvent( $this );
			$this->product_list_view->save();
		}
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Search - result page');
	}
	
	public function getIcon(): string
	{
		return 'magnifying-glass';
	}
	
	public function getCssClass(): string
	{
		return 'light';
	}
	
	
	public function showShortDetails(): string
	{
		$res = '';
		$res .= Tr::_('Search query: "%Q%"', ['Q' => Data_Text::htmlSpecialChars($this->search_query)]);
		$res .= '<br>';
		if($this->found_something) {
			$res .= UI::badge( UI_badge::SUCCESS, Tr::_('Successful search') );
		} else {
			$res .= UI::badge( UI_badge::INFO, Tr::_('Nothing found') );
		}
		
		return $res;
	}
	
	
	public function showLongDetails(): string
	{
		$res = '';
		
		
		if(
			isset($this->result_ids['categories']) &&
			is_array($this->result_ids['categories'])
		) {
			$res .= '<h4>'.Tr::_('Categories').'</h4>';
			
			foreach($this->result_ids['categories'] as $id) {
				$res .= Application_Service_Admin::Category()->renderItemName( $id );
			}
			
			$res .= '<hr>';
		}
		
		$listing = Event_ProductsListView::getFormEvent( $this );
		
		if($listing) {
			$res .= '<h4>'.Tr::_('Products').'</h4>';
			
			$res .=  $listing->showLongDetails();
			
			$res .= '<hr>';
		}
		
		return $res;
	}
	
	
}