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
use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Event_Rejected;
use JetApplication\Complaint_Status;
use JetApplication\Complaint_Status_Rejected;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;

abstract class Core_Complaint_Status_Rejected extends Complaint_Status {
	
	public const CODE = 'rejected';
	protected string $title = 'Rejected';
	protected int $priority = 888;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => true,
		
		'being_processed' => null,
		'clarification_required' => null,
		
		'accepted' => false,
		'money_refund' => false,
		'sent_for_repair' => false,
		'repaired' => false,
		'send_new_products' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-warning';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_Rejected::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public static function getAsPossibleFutureStatus() : ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Rejected') )
					->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Complaint_Status_Rejected::get();
			}
			
			public function noteForCustomerEnabled() : bool
			{
				return true;
			}
			
			public function doNotSendNotificationsSwitchEnabled() : bool
			{
				return true;
			}
		};
	}
	
}