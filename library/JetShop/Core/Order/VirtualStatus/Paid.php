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
use JetApplication\Order_Event_Paid;
use JetApplication\Order_VirtualStatus;
use JetApplication\Order_VirtualStatus_Paid;

abstract class Core_Order_VirtualStatus_Paid extends Order_VirtualStatus
{
	public const CODE = 'paid';
	
	public static function handle(
		EShopEntity_HasStatus_Interface|Order $item,
		bool $handle_event=true,
		array $params=[],
		?Closure $event_setup=null
	): void
	{
		if($item->getPaid()) {
			return;
		}
		
		$item->setFlags([
			'paid' => true,
		]);
		$item->setStatusByFlagState();
		
		if($handle_event) {
			$event = $item->createEvent( Order_Event_Paid::new() );
			if($event_setup) {
				$event_setup( $event );
			}
			$event->handleImmediately();
			
		}
	}
	
	public function getTitle(): string
	{
		return Tr::_('Paid');
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Paid') )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Order_VirtualStatus_Paid::get();
			}
		};
	}
	
}