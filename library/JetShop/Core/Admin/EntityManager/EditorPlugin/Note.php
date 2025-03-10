<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Note;
use JetApplication\EShopEntity_Note;
use JetApplication\EShopEntity_Note_MessageGenerator;

trait Core_Admin_EntityManager_EditorPlugin_Note {
	
	protected ?Admin_Managers_Note $manager = null;
	
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
		$this->manager = Admin_Managers::Note();
		if($this->manager) {
			
			$new_note = $this->newNoteCreator();
			$generated_subject = $this->messageSubjectGenerator();
			
			$this->manager->init(
				new_note: $new_note,
				generated_subject: $generated_subject,
				customer_email_address: $this->item->getEmail(),
				after_add: function( EShopEntity_Note $new_note, &$snippets ) : void {
					$this->afterMessageAdded( $new_note );
					
					$snippets['event-history'] = Admin_Managers::EntityEdit()->renderEventHistory( $this->item );
					$snippets['sent-emails'] = Admin_Managers::EntityEdit()->renderSentEmails( $this->item );
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