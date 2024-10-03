<?php
/**
 *
 */

namespace JetShop;

use JetApplication\Complaint_Event;
use JetApplication\Entity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\Shops_Shop;
use JetApplication\Complaint;


abstract class Core_Complaint_Event_HandlerModule extends Event_HandlerModule
{
	protected Complaint_Event $event;
	protected Shops_Shop $shop;
	protected Complaint $complaint;


	public function init( Entity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$complaint = $event->getComplaint();
		$this->shop = $complaint->getShop();
		$this->complaint = $complaint;
	}

	public function getEvent(): Complaint_Event
	{
		return $this->event;
	}

	public function getComplaint(): Complaint
	{
		return $this->complaint;
	}
}