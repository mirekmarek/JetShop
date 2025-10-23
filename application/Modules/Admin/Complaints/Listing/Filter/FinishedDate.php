<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use JetApplication\Admin_Listing_Filter_DateInterval;
use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Event_Accepted;
use JetApplication\Complaint_Event_AcceptedMoneyRefund;
use JetApplication\Complaint_Event_AcceptedRepaired;
use JetApplication\Complaint_Event_NewProductDispatched;
use JetApplication\Complaint_Event_Rejected;
use JetApplication\Complaint_Event_RepairedProductDispatched;

class Listing_Filter_FinishedDate extends Admin_Listing_Filter_DateInterval
{
	public const KEY = 'finished_date';
	protected string $label = 'Finished Date';
	
	public function generateWhere(): void
	{
		if(
			!$this->from &&
			!$this->till
		) {
			return;
		}
		
		$event_codes = [
			Complaint_Event_Accepted::getCode(),
			Complaint_Event_AcceptedMoneyRefund::getCode(),
			Complaint_Event_Rejected::getCode(),
			Complaint_Event_NewProductDispatched::getCode(),
			Complaint_Event_RepairedProductDispatched::getCode(),
			Complaint_Event_NewProductDispatched::getCode(),
			Complaint_Event_AcceptedRepaired::getCode()
		];
		
		$where = [
			'event' => $event_codes,
		];
		
		if($this->from) {
			$this->from->setTime(0, 0, 0);
			$this->from->setOnlyDate(false);
			
			$where[] = 'AND';
			$where['created >='] = $this->from;
		}
		
		if($this->till) {
			$this->till->setTime(23, 59, 59);
			$this->till->setOnlyDate(false);
			
			$where[] = 'AND';
			$where['created <='] = $this->till;
		}
		
		$complaint_ids = Complaint_Event::dataFetchCol(select:['complaint_id'], where: $where);
		if(!$complaint_ids) {
			$complaint_ids = [0];
		}
		
		$this->listing->addFilterWhere([
			'id' => $complaint_ids,
		]);
	}
}