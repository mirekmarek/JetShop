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
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status_Returned extends OrderDispatch_Status {
	
	public const CODE = 'returned';
	
	public function __construct()
	{
		//TODO:
		$this->title = Tr::_('Returned', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 9;
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
	
	public function createEvent( EShopEntity_Basic|OrderDispatch $item, string $previouse_status_code ): null|EShopEntity_Event|OrderDispatch_Event
	{
		//TODO:
		return null;
	}
	
	
	public function getPossibleFutureStates(): array
	{
		$res = [];
		//TODO:
		return $res;
	}
	
}