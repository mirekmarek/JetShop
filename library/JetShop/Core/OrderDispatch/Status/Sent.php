<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Data_DateTime;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\OrderDispatch_Event_Sent;
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status_Sent extends OrderDispatch_Status {
	
	public const CODE = 'sent';
	protected string $title = 'Sent';
	protected int $priority = 5;
	protected static bool $is_sent = true;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( EShopEntity_Basic|OrderDispatch $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderDispatch_Event
	{
		return OrderDispatch_Event_Sent::new();
	}
	
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public function setupObjectAfterStatusUpdated( EShopEntity_Basic|OrderDispatch $item, array $params=[] ): void
	{
		$item->setDispatchDate( Data_DateTime::now() );
		$item->save();
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}