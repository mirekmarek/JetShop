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
use JetApplication\Complaint_Event_AcceptedRepaired;
use JetApplication\Complaint_Status;
use JetApplication\Complaint_Status_AcceptedRepaired;
use JetApplication\Complaint_Status_AcceptedRepairedDispatched;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;

abstract class Core_Complaint_Status_AcceptedRepaired extends Complaint_Status {
	
	public const CODE = 'accepted_repaired';
	protected string $title = 'Accepted - Repaired';
	protected int $priority = 30;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => false,
		
		'clarification_required' => null,
		'being_processed' => null,
		
		'accepted' => true,
		
		'money_refund' => false,
		'sent_for_repair' => true,
		'repaired' => true,
		'send_new_products' => false,
	];
	
	public function __construct()
	{
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_AcceptedRepaired::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = Complaint_Status_AcceptedRepairedDispatched::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus() : ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Repaired') )
					->setClass( UI_button::CLASS_SUCCESS );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Complaint_Status_AcceptedRepaired::get();
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