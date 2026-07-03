<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use Jet\Data_DateTime;
use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Application_Service_Admin_Complaint;
use JetApplication\Complaint_Event;
use JetApplication\EShopEntity_Basic;
use JetApplication\Order;
use JetApplication\Complaint;
use JetApplication\PDF_TemplateProvider;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Service_Admin_Complaint implements PDF_TemplateProvider, SysServices_Provider_Interface
{
	public const ADMIN_MAIN_PAGE = 'complaints';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Complaint();
	}
	
	public function showOrderComplaints( Order $order ) : void
	{
		Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($order) {
				
				$complaints= Complaint::getByOrder( $order );
				if(!$complaints) {
					return;
				}
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('complaints', $complaints);
				
				echo $view->render('order-complaints');
			}
		);
	}
	
	public function getPDFTemplates(): array
	{
		return [
			new PDFTemplate_ServiceReport(),
			new PDFTemplate_GoodsReceiptProtocol()
		];
	}
	
	public function getSysServicesDefinitions(): array
	{
		$dates_correction = new SysServices_Definition(
			module: $this,
			name: 'Complaints - dates correction',
			description: '',
			service_code: 'dates_correction',
			service: function() {
				$this->datesCorrection();
			}
		);
		
		
		return [$dates_correction];
	}
	
	protected function datesCorrection() : void
	{
		$complaints = Complaint::fetchInstances([
			'date_started >=' => new Data_DateTime('2025-01-01 00:00:00')
		]);
		
		$complaints->getQuery()->setOrderBy(['id']);
		
		foreach($complaints as $complaint) {
			$date_of_receipt_of_clained_goods = null;
			$date_finished = null;
			
			$history = Complaint_Event::fetch(
				[''=>[
					'complaint_id' => $complaint->getId()
				]],
				order_by: ['id']
			);
			
			foreach($history as $h) {
				
				if(
					$date_of_receipt_of_clained_goods===null &&
					$h->getEvent()=='GoodsReceived'
				) {
					$date_of_receipt_of_clained_goods = $h->getDateAdded();
					continue;
				}
				
				if(
					$date_finished===null &&
					in_array(
						$h->getEvent(),
						[
							'Rejected',
							'AcceptedNewGoodsWillBeSend',
							'AcceptedRepaired',
							'AcceptedRepairedDispatched',
							'AcceptedMoneyRefund',
							'NewProductDispatched',
							'Accepted'
						]
					)
				) {
					$date_finished = $h->getDateAdded();
					continue;
				}

				
				if($date_of_receipt_of_clained_goods!==null && $date_finished!==null) {
					break;
				}
				
			}
			
			if(
				$date_of_receipt_of_clained_goods &&
				!$complaint->getDateOfReceiptOfClainedGoods()
			) {
				var_dump($complaint->getId(), $date_of_receipt_of_clained_goods );
				echo PHP_EOL.PHP_EOL;
				Complaint::updateData(
					data: ['date_of_receipt_of_clained_goods'=>$date_of_receipt_of_clained_goods],
					where: ['id'=>$complaint->getId()]
				);
			}
			
			if(
				$date_finished &&
				!$complaint->getDateFinished()
			) {
				var_dump($complaint->getId(), $date_finished );
				echo PHP_EOL.PHP_EOL;
				Complaint::updateData(
					data: ['date_finished'=>$date_finished],
					where: ['id'=>$complaint->getId()]
				);
			}
			
			
		}
		
	}
}