<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\ProductQuestion;
use JetApplication\ProductQuestion_Event;
use JetApplication\ProductQuestion_Event_AnsweredDisplayed;
use JetApplication\ProductQuestion_Status;

abstract class Core_ProductQuestion_Status_AnsweredDisplayed extends ProductQuestion_Status {
	
	public const CODE = 'answered_displayed';
	
	protected static array $flags_map = [
		'assessed' => true,
		'approved' => true,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Answered - displayed', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 20;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( EShopEntity_Basic|ProductQuestion $item, EShopEntity_Status $previouse_status ): ?ProductQuestion_Event
	{
		return $item->createEvent( ProductQuestion_Event_AnsweredDisplayed::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		return $res;
	}
	
}