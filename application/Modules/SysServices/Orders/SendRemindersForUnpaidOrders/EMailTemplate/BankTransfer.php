<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Orders\SendRemindersForUnpaidOrders;

use Jet\Tr;
use JetApplication\Application_Service_EShop;
use JetApplication\Order_EMailTemplate;

class EMailTemplate_BankTransfer extends Order_EMailTemplate {
	
	protected function init(): void
	{
		$this->setInternalName( Tr::_('Unpaid order reminder - bank transfer') );
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
		
		$message_property = $this->addProperty('qr_payment_info', Tr::_('QR Payment Info') );
		$message_property->setPropertyValueCreator( function() : string {
			$qr_payment = Application_Service_EShop::QRPayment( $this->order->getEshop() );
			return $qr_payment?->generateReminderEmailInfoText( $this->order )??'';
			
		} );
		
	}
}