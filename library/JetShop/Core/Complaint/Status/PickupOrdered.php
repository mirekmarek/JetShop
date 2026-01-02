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
use JetApplication\Complaint_Event_PickupOrdered;
use JetApplication\Complaint_Status;
use JetApplication\Complaint_Status_Cancelled;
use JetApplication\Complaint_Status_PickupOrdered;
use JetApplication\Complaint_Status_GoodsReceived;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;

abstract class Core_Complaint_Status_PickupOrdered extends Complaint_Status {
	
	public const CODE = 'pickup_ordered';
	protected string $title = 'Pickup ordered';
	protected int $priority = 10;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => null,
		'rejected' => null,
		
		'being_processed' => null,
		'clarification_required' => null,
		
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
		return $item->createEvent( Complaint_Event_PickupOrdered::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = Complaint_Status_GoodsReceived::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_Cancelled::getAsPossibleFutureStatus();
		
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus() : ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Pickup ordered') )
					->setClass( UI_button::CLASS_PRIMARY )
					->setIcon('truck-ramp-box');
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Complaint_Status_PickupOrdered::get();
			}
		};
	}
}