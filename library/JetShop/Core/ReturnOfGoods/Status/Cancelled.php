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
use JetApplication\ReturnOfGoods_Event_Cancelled;
use JetApplication\ReturnOfGoods_Status;

abstract class Core_ReturnOfGoods_Status_Cancelled extends ReturnOfGoods_Status {
	
	public const CODE = 'cancelled';
	protected string $title = 'Cancelled';
	protected int $priority = 60;
	
	protected static array $flags_map = [
		'cancelled' => true,
		
		'completed' => null,
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => null,
		
		'accepted' => null,
		
		'money_refund' => null,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	
	public function createEvent( ReturnOfGoods|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?ReturnOfGoods_Event
	{
		return $item->createEvent( ReturnOfGoods_Event_Cancelled::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}