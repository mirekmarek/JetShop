<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Application_Service_Admin;
use JetApplication\Application_Service_Admin_Note;
use JetApplication\EShopEntity_Note;
use JetApplication\EShopEntity_Note_MessageGenerator;

trait Core_Admin_EntityManager_EditorPlugin_Note {
	
	protected ?Application_Service_Admin_Note $manager = null;
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	public function handleOnlyIfItemIsEditable() : bool
	{
		return false;
	}
	
	protected function init() : void
	{
		$this->manager = Application_Service_Admin::Note();
		if($this->manager) {
			
			$new_note = $this->newNoteCreator();
			$generated_subject = $this->messageSubjectGenerator();
			
			$this->manager->init(
				new_note: $new_note,
				generated_subject: $generated_subject,
				customer_email_address: $this->item->getEmail(),
				after_add: function( EShopEntity_Note $new_note, &$snippets ) : void {
					$this->afterMessageAdded( $new_note );
					
					$snippets['event-history'] = Application_Service_Admin::EntityEdit()->renderEventHistory( $this->item );
					$snippets['sent-emails'] = Application_Service_Admin::EntityEdit()->renderSentEmails( $this->item );
				}
			);
			
			foreach($this->initMessageGenerators() as $message_generator) {
				$this->manager->addMessageGenerator( $message_generator );
			}
		}
	}
	
	/**
	 * @return EShopEntity_Note_MessageGenerator[]
	 */
	public function initMessageGenerators() : array
	{
		return [];
	}
	
	public function renderDialog() : string
	{
		return $this->manager?->showDialog()??'';
	}
	
	public function renderButton() : string
	{
		return $this->manager?->showButton()??'';
	}
	
	public function handle(): void
	{
		$this->manager?->handle();
	}
	
}