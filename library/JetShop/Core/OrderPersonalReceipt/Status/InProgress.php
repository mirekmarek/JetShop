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
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_Event;
use JetApplication\OrderPersonalReceipt_Event_PreparationStarted;
use JetApplication\OrderPersonalReceipt_Status;

abstract class Core_OrderPersonalReceipt_Status_InProgress extends OrderPersonalReceipt_Status {
	
	public const CODE = 'in_progress';
	
	protected static bool $is_rollback_possible = true;
	
	
	public function __construct()
	{
		$this->title = Tr::_('In progress', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 2;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return '';
	}
	
	public function getShowAdminCSSStyle() : string
	{
		//TODO:
		return '';
	}
	
	public function createEvent( EShopEntity_Basic|OrderPersonalReceipt $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderPersonalReceipt_Event
	{
		return OrderPersonalReceipt_Event_PreparationStarted::new();
	}
	
	
	public function getPossibleFutureStates(): array
	{
		$res = [];
		//TODO:
		return $res;
	}
	
}