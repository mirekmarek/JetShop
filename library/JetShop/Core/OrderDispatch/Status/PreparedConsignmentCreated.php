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

abstract class Core_OrderDispatch_Status_PreparedConsignmentCreated extends OrderDispatch_Status {
	
	public const CODE = 'prepared_consignment_created';
	
	protected static bool $is_in_progress = true;
	protected static bool $is_prepared = true;
	protected static bool $is_consignment_created = true;
	protected static bool $is_rollback_possible = true;
	protected static bool $can_be_cancelled = true;
	
	public function __construct()
	{
		$this->title = Tr::_('Ready to send', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 4;
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
	
	public function setupObjectAfterStatusUpdated( EShopEntity_Basic|OrderDispatch $item, array $params=[] ): void
	{
		$item->setConsignmentId( $params['consignment_id'] );
		$item->setTrackingNumber( $params['tracking_number'] );
		$item->save();
	}
	
}