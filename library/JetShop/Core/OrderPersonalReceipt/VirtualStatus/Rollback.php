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
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_Event;
use JetApplication\OrderPersonalReceipt_Status_Pending;
use JetApplication\OrderPersonalReceipt_VirtualStatus;
use Closure;

abstract class Core_OrderPersonalReceipt_VirtualStatus_Rollback extends OrderPersonalReceipt_VirtualStatus {
	
	public const CODE = 'rollback';
	protected string $title = 'Rollback';
	protected int $priority = 4;
	protected static bool $is_rollback_possible = true;
	
	public function getTitle() : string
	{
		return 'Rollback';
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function createEvent( EShopEntity_Basic|OrderPersonalReceipt $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderPersonalReceipt_Event
	{
		return null;
	}
	
	public static function handle(
		EShopEntity_HasStatus_Interface|OrderPersonalReceipt $item,
		bool $handle_event=true,
		array $params=[],
		?Closure $event_setup=null
	) : void
	{
		$item->setStatus(OrderPersonalReceipt_Status_Pending::get(), handle_event: false );
		
		$item->setHeadedOverDate( null );
		$item->setHeadedOverDateTime( null );
		$item->save();
	}
	
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Rollback') )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Core_OrderPersonalReceipt_VirtualStatus_Rollback::get();
			}
		};
	}
	
}