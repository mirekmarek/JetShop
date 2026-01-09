<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use Jet\UI;
use Jet\UI_button;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\ProductQuestion;
use JetApplication\ProductQuestion_Event;
use JetApplication\ProductQuestion_Event_Rejected;
use JetApplication\ProductQuestion_Status;
use JetApplication\ProductQuestion_Status_Rejected;

abstract class Core_ProductQuestion_Status_Rejected extends ProductQuestion_Status {
	
	public const CODE = 'rejected';
	protected string $title = 'Rejected';
	protected int $priority = 30;
	
	protected static array $flags_map = [
		'answered' => false,
		'display' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function createEvent( EShopEntity_Basic|ProductQuestion $item, EShopEntity_Status $previouse_status ): ?ProductQuestion_Event
	{
		return $item->createEvent( ProductQuestion_Event_Rejected::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus
		{
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Reject') )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ProductQuestion_Status_Rejected::get();
			}
		};
	}
	
}