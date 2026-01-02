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
use JetApplication\Complaint_Event_HandedOverToTechnician;
use JetApplication\Complaint_Status;
use JetApplication\Complaint_Status_AcceptedIrreparable;
use JetApplication\Complaint_Status_AcceptedRepaired;
use JetApplication\Complaint_Status_Cancelled;
use JetApplication\Complaint_Status_HandedOverToTechnician;
use JetApplication\Complaint_Status_Rejected;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;

abstract class Core_Complaint_Status_HandedOverToTechnician extends Complaint_Status {
	
	public const CODE = 'handed_over_to_technician';
	protected string $title = 'Handed over to technician';
	protected int $priority = 20;
	
	protected static array $flags_map = [
		'completed' => true,
		'cancelled' => false,
		
		'clarification_required' => true,
		'being_processed' => true,
		'rejected' => null,
		'accepted' => null,
		'money_refund' => null,
		'sent_for_repair' => null,
		'repaired' => null,
		'send_new_products' => null,
	
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_HandedOverToTechnician::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		
		$res[] = Complaint_Status_AcceptedRepaired::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_AcceptedIrreparable::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_Rejected::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus() : ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Handed over to technician') )->setClass( UI_button::CLASS_SUCCESS );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Complaint_Status_HandedOverToTechnician::get();
			}
			
			public function noteForCustomerEnabled() : bool
			{
				return false;
			}
			
			public function doNotSendNotificationsSwitchEnabled() : bool
			{
				return false;
			}
			
		};
	}
	
}