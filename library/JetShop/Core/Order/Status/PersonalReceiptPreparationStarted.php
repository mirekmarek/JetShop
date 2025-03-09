<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Event_PersonalReceiptPreparationStarted;
use JetApplication\Order_Status;

abstract class Core_Order_Status_PersonalReceiptPreparationStarted extends Order_Status {
	
	public const CODE = 'personal_receipt_preparation_started';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'dispatched' => false,
		'delivered' => false,
		'returned' => false,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => true,
		'dispatch_started' => true,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Personal Receipt Preparation Started', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 40;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return '';
	}
	
	public function getShowAdminCSSStyle() : string
	{
		return 'background-color: #00ddc1;';
	}
	
	public function createEvent( Order|EShopEntity_Basic $item, string $previouse_status_code ) : Order_Event
	{
		return $item->createEvent( Order_Event_PersonalReceiptPreparationStarted::new() );
	}
	
	public static function resolve( EShopEntity_HasStatus_Interface|Order $item ) : bool
	{
		if(!$item->getDeliveryMethod()->isPersonalTakeover()) {
			return false;
		}
		
		return parent::resolve( $item );
	}
	
}