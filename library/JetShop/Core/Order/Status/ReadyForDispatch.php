<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use Jet\UI;
use Jet\UI_button;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Event_ReadyForDispatch;
use JetApplication\Order_Status;
use JetApplication\Order_Status_Cancelled;

abstract class Core_Order_Status_ReadyForDispatch extends Order_Status {
	
	public const CODE = 'ready_for_dispatch';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'dispatched' => false,
		'dispatch_started' => false,
		'delivered' => false,
		'returned' => false,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => true,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Ready for dispatch', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 30;
	}
	
	public function createEvent( Order|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ) : Order_Event
	{
		return $item->createEvent( Order_Event_ReadyForDispatch::new() );
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-processing';
	}
	
	public static function resolve( EShopEntity_HasStatus_Interface|Order $item ) : bool
	{
		if($item->getDeliveryMethod()?->isPersonalTakeover()) {
			return false;
		}
		
		return parent::resolve( $item );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Cancel') )
					->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Order_Status_Cancelled::get();
			}
			
			
			public function noteForCustomerEnabled() : bool
			{
				return true;
			}
			
			public function doNotSendNotificationsSwitchEnabled() : bool
			{
				return true;
			}
			
		};
		
		return $res;
	}
	
}