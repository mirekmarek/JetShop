<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\NewOrder;

use Jet\Tr;
use JetApplication\Marketing_PromoArea;
use JetApplication\Order_EMailTemplate;

class EMailTemplate extends Order_EMailTemplate {
	
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Order - confirmation'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
		
		$this->addProperty('marketing_position', Tr::_('Marketing position'))
			->setPropertyValueCreator( function( ) : string {
				return Marketing_PromoArea::render('order_confirmation_info', $this->order->getEshop());
			} );
		
	}
	
	/*
	public function initTest( EShop $eshop ): void
	{
		$id = 21;
		
		$this->order = Order::get($id);
	}
	*/
	
	
}