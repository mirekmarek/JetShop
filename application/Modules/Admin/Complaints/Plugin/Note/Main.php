<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use JetApplication\Admin_EntityManager_EditorPlugin_Note;
use JetApplication\Complaint;
use JetApplication\Complaint_Event_MessageForCustomer;
use JetApplication\Complaint_Note;
use JetApplication\EShopEntity_Note;
use JetApplication\EShopEntity_Note_MessageGenerator;

class Plugin_Note_Main extends Plugin
{
	public const KEY = 'note';
	
	use Admin_EntityManager_EditorPlugin_Note;
	
	protected function newNoteCreator() : Complaint_Note
	{
		/**
		 * @var Complaint $item
		 */
		$item = $this->item;
		$new_note = new Complaint_Note();
		$new_note->setComplaint( $item );
		
		return $new_note;
	}
	
	protected function messageSubjectGenerator() : string
	{
		/**
		 * @var \JetApplicationModule\Events\Complaint\MessageForCustomer\Main $event_handler
		 * @var Complaint $item
		 */
		
		$item = $this->item;
		$event_handler = $this->item->createEvent( Complaint_Event_MessageForCustomer::new() )->getHandlerModule();
		$template = $event_handler->getEMailTemplates()[0];
		$template->setComplaint( $item );
		
		return $template->createEmail( $item->getEshop() )?->getSubject()??'';
	}
	
	protected function afterMessageAdded( EShopEntity_Note|Complaint_Note $new_note ) : void
	{
		/**
		 * @var Complaint $item
		 */
		$item = $this->item;
		
		$item->newNote( $new_note );
	}
	
	/**
	 * @return EShopEntity_Note_MessageGenerator[]
	 */
	public function initMessageGenerators() : array
	{
		return Plugin_Note_MessageGenerator::initGenerators( $this->view, $this->item );
	}
	
	
}