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
use JetApplication\OrderDispatch_Event_Delivered;
use JetApplication\OrderDispatch_Status;

abstract class Core_OrderDispatch_Status_Delivered extends OrderDispatch_Status {
	
	public const CODE = 'delivered';
	
	public function __construct()
	{
		//TODO:
		$this->title = Tr::_('Delivered', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 7;
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
	
	public function createEvent( EShopEntity_Basic|OrderDispatch $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderDispatch_Event
	{
		return OrderDispatch_Event_Delivered::new();
	}
	
	
	public function getPossibleFutureStates(): array
	{
		$res = [];
		//TODO:
		return $res;
	}
	
}