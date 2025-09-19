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
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\OrderDispatch_Event_Delivered;
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status_Delivered extends OrderDispatch_Status {
	
	public const CODE = 'delivered';
	protected string $title = 'Delivered';
	protected int $priority = 7;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( EShopEntity_Basic|OrderDispatch $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderDispatch_Event
	{
		return OrderDispatch_Event_Delivered::new();
	}
	
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		//TODO:
		return $res;
	}
	
}