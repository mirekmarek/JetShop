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
use JetApplication\InappropriateContentReporting;
use JetApplication\InappropriateContentReporting_Event;
use JetApplication\InappropriateContentReporting_Event_Rejected;
use JetApplication\InappropriateContentReporting_Status;
use JetApplication\InappropriateContentReporting_Status_Rejected;

abstract class Core_InappropriateContentReporting_Status_Rejected extends InappropriateContentReporting_Status {
	
	public const CODE = 'rejected';
	protected string $title = 'Rejected';
	protected int $priority = 30;
	
	protected static array $flags_map = [
		'assessed' => true,
		'approved' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function createEvent( EShopEntity_Basic|InappropriateContentReporting $item, EShopEntity_Status $previouse_status ): ?InappropriateContentReporting_Event
	{
		return $item->createEvent( InappropriateContentReporting_Event_Rejected::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Reject') )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return InappropriateContentReporting_Status_Rejected::get();
			}
		};
	}
}