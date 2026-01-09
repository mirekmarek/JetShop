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
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status_Canceled extends OrderDispatch_Status {
	
	public const CODE = 'canceled';
	protected string $title = 'Canceled';
	protected int $priority = 12;
	
	protected static bool $is_rollback_possible = true;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function createEvent( EShopEntity_Basic|OrderDispatch $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderDispatch_Event
	{
		//TODO:
		return null;
	}
	
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		//TODO:
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		// TODO: Implement getAsPossibleFutureStatus() method.
		return null;
	}
	
}