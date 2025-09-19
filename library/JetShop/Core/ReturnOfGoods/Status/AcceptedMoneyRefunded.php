<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods_Event_DoneAccepted;
use JetApplication\ReturnOfGoods_Status;

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
	
}