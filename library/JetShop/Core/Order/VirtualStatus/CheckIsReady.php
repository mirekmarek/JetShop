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
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use Closure;
use JetApplication\Order;
use JetApplication\Order_Event_NotReadyForDispatch;
use JetApplication\Order_Status_ReadyForDispatch;
use JetApplication\Order_VirtualStatus;
use JetApplication\Order_VirtualStatus_CheckIsReady;

abstract class Core_Order_VirtualStatus_CheckIsReady extends Order_VirtualStatus
{
	public const CODE = 'check_is_ready';
	
	public static function handle(
		EShopEntity_HasStatus_Interface|Order $item,
		bool $handle_event=true,
		array $params=[],
		?Closure $event_setup=null
	): void
	{
		if($item->getDeliveryMethod()->isEDelivery()) {
			return;
		}
		
		if(
			(
				$item->getPaid() ||
				!$item->getPaymentRequired()
			) &&
			$item->getAllItemsAvailable()
		) {
			if(!$item->getReadyForDispatch()) {
				$item->setStatus( Order_Status_ReadyForDispatch::get() );
			}
		} else {
			if($item->getReadyForDispatch()) {
				
				$item->setFlags(
					[
						'ready_for_dispatch' => false,
						'dispatch_started' => false,
						'dispatched' => false,
						'delivered' => false,
					]
				);
				$item->setStatusByFlagState();
				
				if($handle_event) {
					$event = $item->createEvent( Order_Event_NotReadyForDispatch::new() );
					if($event_setup) {
						$event_setup( $event );
					}
					$event->handleImmediately();
					
				}
			}
		}
	}
	
	public function getTitle(): string
	{
		return Tr::_('Check is ready');
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Check is ready') )->setClass( UI_button::CLASS_INFO );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Order_VirtualStatus_CheckIsReady::get();
			}
		};
	}
	
}