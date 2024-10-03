<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Stats\CookieConsents;

use Jet\Application;
use Jet\Data_DateTime;
use Jet\Http_Request;
use Jet\Locale;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Shop_CookieSettings_Evidence_Agree;
use JetApplication\Shop_CookieSettings_Evidence_Disagree;
use JetApplication\Shops;
use XLSXWriter\XLSXWriter;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		Admin_Managers::UI()->initBreadcrumb();
		
		
		$GET = Http_Request::GET();
		
		
		$shop_key = $GET->getString('shop', '', array_keys(Shops::getScope()));
		$shop = null;
		if($shop_key) {
			$shop = Shops::get( $shop_key );
		}
		
		$date_from = new Data_DateTime( $GET->getString('date_from', date('Y-m-01',strtotime('last month'))) );
		$date_till = new Data_DateTime( $GET->getString('date_till', date('Y-m-d')) );
		$date_till->setTime(23,59,59);
		
		$where = [
			'date_time >=' => $date_from,
			'AND',
			'date_time <=' => $date_till
		];
		if($shop) {
			$where[] = 'AND';
			$where[] = $shop->getWhere();
		}

		
		
		$q = Shop_CookieSettings_Evidence_Agree::createQuery();
		$q->setSelect([
			'IP',
			'date_time',
			'groups',
			'complete_agree'
		]);
		$q->setWhere($where);
		
		
		$agree_data = Shop_CookieSettings_Evidence_Agree::dataFetchAll(
			select: [
				'IP',
				'date_time',
				'groups',
				'complete_agree'
			],
			where: $where
		);

		
		$disagree_data = Shop_CookieSettings_Evidence_Disagree::dataFetchAll(
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
			
			if($shop) {
				$file_name = 'cookies_agree_'.$shop->getKey().'_'.$date_from.'_'.$date_till.'_'.date('Ymd');
			} else {
				$file_name = 'cookies_agree_'.$date_from.'_'.$date_till.'_'.date('Ymd');
			}
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
			header('Cache-Control: max-age=0');
			
			
			$writer->writeToStdOut();
			
			Application::end();
		}
		
		$this->view->setVar('shop_key', $shop_key);
		$this->view->setVar('shop', $shop );
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