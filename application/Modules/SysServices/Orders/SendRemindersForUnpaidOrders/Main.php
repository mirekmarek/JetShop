<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Orders\SendRemindersForUnpaidOrders;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Application_Service_General;
use JetApplication\EMail_TemplateProvider;
use JetApplication\EShops;
use JetApplication\Order;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface, EMail_TemplateProvider
{
	public function getSysServicesDefinitions(): array
	{
		$service = new SysServices_Definition(
			module: $this,
			name: Tr::_('Order payment reminders'),
			description: Tr::_('send reminders (e-mails) for unpaid orders'),
			service_code: 'perform',
			service: function() {
				$this->sendEmails();
			}
		);
		
		$service->setIsPeriodicallyTriggeredService( true );
		$service->setServiceRequiresEshopDesignation( true );
		
		return [
			$service
		];
	}
	
	public function sendEmails() : void
	{
		$eshop = EShops::getCurrent();
		
		$calendar = Application_Service_General::Calendar();
		
		$date_from = $calendar->getPrevBusinessDate( eshop: $eshop, number_of_working_days: 2 );
		$date_to = $calendar->getPrevBusinessDate( eshop: $eshop, number_of_working_days: 1 );
		
		$date_from->setTime(0, 0, 0);
		$date_to->setTime(23, 59, 59);

		
		$order_ids = Order::dataFetchCol(
			select: ['id'],
			where: [
				$eshop->getWhere(),
				'AND',
				'cancelled' => false,
				'AND',
				'payment_required' => true,
				'AND',
				'paid' => false,
				'AND',
				[
					'date_purchased >=' => $date_from,
					'AND',
					'date_purchased <=' => $date_to,
				]
			]
		);
		
		
		foreach ($order_ids as $order_id) {
			
			$order = Order::get( $order_id );
			if(!$order) {
				continue;
			}
			
			$pm = $order->getPaymentMethod();
			if(
				!$pm ||
				$pm->getKind()->isLoan()
			) {
				continue;
			}
			
			
			if($pm->getKind()->isBankTransfer()) {
				$email_template = new EMailTemplate_BankTransfer();
			} else {
				$email_template = new EMailTemplate_Online();
			}
			
			$email_template->setOrder( $order );
			$email = $email_template->createEmail( $order->getEshop() );
			
			if(
				!$email ||
				$email->hasBeenSent()
			) {
				continue;
			}
			
			echo $order_id.PHP_EOL;
			
			$email->send();
		}
	}
	
	public function getEMailTemplates(): array
	{
		$tr = new EMailTemplate_BankTransfer();
		$online = new EMailTemplate_Online();
		
		return [
			$tr,
			$online,
		];
	}
}