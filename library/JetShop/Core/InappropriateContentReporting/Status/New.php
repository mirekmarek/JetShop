<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\InappropriateContentReporting;
use JetApplication\InappropriateContentReporting_Event;
use JetApplication\InappropriateContentReporting_Event_New;
use JetApplication\InappropriateContentReporting_Status;
use JetApplication\InappropriateContentReporting_Status_Approved;
use JetApplication\InappropriateContentReporting_Status_Rejected;

abstract class Core_InappropriateContentReporting_Status_New extends InappropriateContentReporting_Status {
	
	public const CODE = 'new';
	protected string $title = 'New';
	protected int $priority = 10;
	
	protected static array $flags_map = [
		'assessed' => false,
		'approved' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function createEvent( EShopEntity_Basic|InappropriateContentReporting $item, EShopEntity_Status $previouse_status ): ?InappropriateContentReporting_Event
	{
		return $item->createEvent( InappropriateContentReporting_Event_New::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = InappropriateContentReporting_Status_Approved::getAsPossibleFutureStatus();
		$res[] = InappropriateContentReporting_Status_Rejected::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}