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
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods_Event_DoneAccepted;
use JetApplication\ReturnOfGoods_Status;
use JetApplication\ReturnOfGoods_Status_AcceptedMoneyRefunded;

abstract class Core_ReturnOfGoods_Status_AcceptedMoneyRefunded extends ReturnOfGoods_Status {
	
	public const CODE = 'accepted_money_refunded';
	protected string $title = 'Accepted - Money Refunded';
	protected int $priority = 70;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => false,
		
		'accepted' => true,
		
		'money_refund' => true,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( EShopEntity_Basic|ReturnOfGoods $item, EShopEntity_Status $previouse_status ): ?ReturnOfGoods_Event
	{
		return $item->createEvent( ReturnOfGoods_Event_DoneAccepted::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Accepted - money refunded') )->setClass( UI_button::CLASS_SUCCESS );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ReturnOfGoods_Status_AcceptedMoneyRefunded::get();
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
		
	}
	
}