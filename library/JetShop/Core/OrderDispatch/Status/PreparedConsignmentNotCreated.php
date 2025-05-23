<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status_PreparedConsignmentNotCreated extends OrderDispatch_Status {
	
	public const CODE = 'prepared_consignment_not_created';
	
	protected static bool $is_in_progress = true;
	protected static bool $is_prepared = true;
	protected static bool $is_ready_to_create_consignment = false;
	protected static bool $can_create_consignment = true;
	protected static bool $is_rollback_possible = true;
	protected static bool $can_be_cancelled = true;
	
	
	public function __construct()
	{
		$this->title = Tr::_('Waiting for consignment to be created at the carrier', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 2;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
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
	
}