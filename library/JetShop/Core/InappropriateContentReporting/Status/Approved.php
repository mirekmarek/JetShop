<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\InappropriateContentReporting;
use JetApplication\InappropriateContentReporting_Event;
use JetApplication\InappropriateContentReporting_Event_Approved;
use JetApplication\InappropriateContentReporting_Status;

abstract class Core_InappropriateContentReporting_Status_Approved extends InappropriateContentReporting_Status {
	
	public const CODE = 'approved';
	protected string $title = 'Approved';
	protected int $priority = 20;
	
	protected static array $flags_map = [
		'assessed' => true,
		'approved' => true,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( EShopEntity_Basic|InappropriateContentReporting $item, EShopEntity_Status $previouse_status ): ?InappropriateContentReporting_Event
	{
		return $item->createEvent( InappropriateContentReporting_Event_Approved::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		return $res;
	}
	
}