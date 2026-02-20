<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\OrderDispatch_Event_OnTheWay;
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status_OnTheWay extends OrderDispatch_Status {
	
	public const CODE = 'on_the_way';
	protected string $title = 'On the way';
	protected int $priority = 6;
	protected static bool $is_sent = true;

	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( EShopEntity_Basic|OrderDispatch $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderDispatch_Event
	{
		return $item->createEvent( OrderDispatch_Event_OnTheWay::new() );
	}
	
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}