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
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event_Rollback;
use JetApplication\MoneyRefund_Status_New;
use JetApplication\MoneyRefund_VirtualStatus;
use Closure;
use JetApplication\MoneyRefund_VirtualStatus_Rollback;

abstract class Core_MoneyRefund_VirtualStatus_Rollback extends MoneyRefund_VirtualStatus
{
	public const CODE = 'rollback';
	
	public static function handle(
		EShopEntity_HasStatus_Interface|MoneyRefund $item,
		bool $handle_event = true,
		array $params = [],
		?Closure $event_setup = null
	): void
	{
		$item->setStatus( MoneyRefund_Status_New::get(), handle_event: false );
		
		if($handle_event) {
			$event = $item->createEvent( MoneyRefund_Event_Rollback::new() );
			if($event_setup) {
				$event_setup( $event );
			}
			$event->handleImmediately();
			
		}
	}
	
	public function getTitle(): string
	{
		return Tr::_('Rollback');
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Rollback') )->setClass( UI_button::CLASS_LIGHT );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return MoneyRefund_VirtualStatus_Rollback::get();
			}
		};
	}
	
}