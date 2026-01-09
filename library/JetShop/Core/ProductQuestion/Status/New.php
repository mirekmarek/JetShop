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
use JetApplication\ProductQuestion;
use JetApplication\ProductQuestion_Event;
use JetApplication\ProductQuestion_Event_New;
use JetApplication\ProductQuestion_Status;
use JetApplication\ProductQuestion_Status_AnsweredDisplayed;
use JetApplication\ProductQuestion_Status_AnsweredNotDisplayed;
use JetApplication\ProductQuestion_Status_Rejected;

abstract class Core_ProductQuestion_Status_New extends ProductQuestion_Status {
	
	public const CODE = 'new';
	protected string $title = 'New';
	protected int $priority = 10;
	
	protected static array $flags_map = [
		'answered' => false,
		'display' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function createEvent( EShopEntity_Basic|ProductQuestion $item, EShopEntity_Status $previouse_status ): ?ProductQuestion_Event
	{
		return $item->createEvent( ProductQuestion_Event_New::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = ProductQuestion_Status_AnsweredDisplayed::getAsPossibleFutureStatus();
		$res[] = ProductQuestion_Status_AnsweredNotDisplayed::getAsPossibleFutureStatus();
		$res[] = ProductQuestion_Status_Rejected::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
	
}