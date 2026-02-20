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
use JetApplication\OrderDispatch_Event_PreparedConsignmentNotCreated;
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status_PreparedConsignmentNotCreated extends OrderDispatch_Status {
	
	public const CODE = 'prepared_consignment_not_created';
	protected string $title = 'Waiting for consignment to be created at the carrier';
	protected int $priority = 2;
	
	protected static bool $is_in_progress = true;
	protected static bool $is_prepared = true;
	protected static bool $is_ready_to_create_consignment = false;
	protected static bool $can_create_consignment = true;
	protected static bool $is_rollback_possible = true;
	protected static bool $can_be_cancelled = true;
	
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function createEvent( EShopEntity_Basic|OrderDispatch $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderDispatch_Event
	{
		return $item->createEvent( OrderDispatch_Event_PreparedConsignmentNotCreated::new() );
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