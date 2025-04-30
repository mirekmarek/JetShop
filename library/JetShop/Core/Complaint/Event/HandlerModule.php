<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Complaint_Event;
use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\EShop;
use JetApplication\Complaint;
use JetApplication\Complaint_EMailTemplate;


abstract class Core_Complaint_Event_HandlerModule extends Event_HandlerModule
{
	protected Complaint_Event $event;
	protected EShop $eshop;
	protected Complaint $complaint;


	public function init( EShopEntity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$complaint = $event->getComplaint();
		$this->eshop = $complaint->getEshop();
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
	
	public function sendEMail( Complaint_EMailTemplate $template ) : bool
	{
		$template->setEvent($this->event);
		$email = $template->createEmail( $this->getEvent()->getEshop() );
		
		return $email?->send()??true;
	}
}