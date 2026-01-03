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
use JetApplication\Complaint_Event_SentForReview;
use JetApplication\Complaint_Status;
use JetApplication\Complaint_Status_AcceptedMoneyRefunded;
use JetApplication\Complaint_Status_AcceptedNewProduct;
use JetApplication\Complaint_Status_AcceptedRepairedDispatched;
use JetApplication\Complaint_Status_SentForReview;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\Complaint_Status_Rejected;

abstract class Core_Complaint_Status_SentForReview extends Complaint_Status {
	
	public const CODE = 'sent_for_review';
	protected string $title = 'Sent For Review';
	protected int $priority = 21;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => false,
		
		'clarification_required' => false,
		'being_processed' => true,
		
		'accepted' => false,
		
		'money_refund' => false,
		'sent_for_repair' => false,
		'repaired' => false,
		'send_new_products' => false,
	
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_SentForReview::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = Complaint_Status_AcceptedMoneyRefunded::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_AcceptedNewProduct::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_Rejected::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_AcceptedRepairedDispatched::getAsPossibleFutureStatus();
		
		//$res[] = Complaint_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus() : ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Sent For Review') )->setClass( UI_button::CLASS_INFO );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return Complaint_Status_SentForReview::get();
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