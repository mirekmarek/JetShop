<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Stats\CookieConsents;


use Jet\Application;
use Jet\Data_DateTime;
use Jet\Http_Request;
use Jet\Locale;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use JetApplication\EShop_CookieSettings_Evidence_Agree;
use JetApplication\EShop_CookieSettings_Evidence_Disagree;
use JetApplication\EShops;
use XLSXWriter\XLSXWriter;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$GET = Http_Request::GET();
		
		
		$eshop_key = $GET->getString('eshop', '', array_keys(EShops::getScope()));
		$eshop = null;
		if($eshop_key) {
			$eshop = EShops::get( $eshop_key );
		}
		
		$date_from = new Data_DateTime( $GET->getString('date_from', date('Y-m-01',strtotime('last month'))) );
		$date_till = new Data_DateTime( $GET->getString('date_till', date('Y-m-d')) );
		$date_till->setTime(23,59,59);
		
		$where = [
			'date_time >=' => $date_from,
			'AND',
			'date_time <=' => $date_till
		];
		if($eshop) {
			$where[] = 'AND';
			$where[] = $eshop->getWhere();
		}

		
		
		$q = EShop_CookieSettings_Evidence_Agree::createQuery();
		$q->setSelect([
			'IP',
			'date_time',
			'groups',
			'complete_agree'
		]);
		$q->setWhere($where);
		
		
		$agree_data = EShop_CookieSettings_Evidence_Agree::dataFetchAll(
			select: [
				'IP',
				'date_time',
				'groups',
				'complete_agree'
			],
			where: $where
		);

		
		$disagree_data = EShop_CookieSettings_Evidence_Disagree::dataFetchAll(
			select: [
				'IP',
				'date_time'
			],
			where: $where
		);
		
		
		$disagree_count = count($disagree_data);
		
		$agree_count = count($agree_data);
		$non_complete_agree_count = 0;
		$complete_agree_count = 0;
		$agree_by_groups = [];
		
		
		foreach($agree_data as $d) {
			if( $d['complete_agree'] ) {
				$complete_agree_count++;
			} else {
				$non_complete_agree_count++;
			}
			
			$_groups = explode( '|', $d['groups'] );
			foreach( $_groups as $_group ) {
				if( !isset( $agree_by_groups[$_group] ) ) {
					$agree_by_groups[$_group] = 0;
				}
				
				$agree_by_groups[$_group]++;
			}
		}
		

		if($GET->exists('export')) {
			$sheet_data = [];
			
			$sheet_data[] = [
				Tr::_('Date and time'),
				Tr::_('IP'),
				Tr::_('Allowed groups'),
				Tr::_('Complete agreement')
			];
			
			foreach($agree_data as $d) {
				$sheet_data[] = [
					Locale::dateAndTime($d['date_time']),
					$d['IP'],
					$d['groups'],
					Tr::_($d['complete_agree']?'yes':'no')
				];
			}
			
			
			$writer = new XLSXWriter();
			
			
			$writer->writeSheet( $sheet_data );
			
			if($eshop) {
				$file_name = 'cookies_agree_'.$eshop->getKey().'_'.$date_from.'_'.$date_till.'_'.date('Ymd');
			} else {
				$file_name = 'cookies_agree_'.$date_from.'_'.$date_till.'_'.date('Ymd');
			}
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
			header('Cache-Control: max-age=0');
			
			
			$writer->writeToStdOut();
			
			Application::end();
		}
		
		$this->view->setVar('eshop_key', $eshop_key);
		$this->view->setVar('eshop', $eshop );
		$this->view->setVar('date_from', $date_from->format('Y-m-d'));
		$this->view->setVar('date_till', $date_till->format('Y-m-d'));
		
		
		$this->view->setVar('disagree_count', $disagree_count);
		
		$this->view->setVar('agree_count', $agree_count);
		$this->view->setVar('non_complete_agree_count', $non_complete_agree_count);
		$this->view->setVar('complete_agree_count', $complete_agree_count);
		$this->view->setVar('agree_by_groups', $agree_by_groups);
		
		
		
		$this->output('default');
	}
}