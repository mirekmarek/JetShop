<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Note;
use JetApplication\Complaint_Note;
use JetApplication\Complaint;

class Handler_Note_Main extends Handler
{
	public const KEY = 'note';
	
	protected bool $has_dialog = true;

	protected ?Admin_Managers_Note $manager = null;
	
	protected function init() : void
	{
		
		$this->manager = Admin_Managers::Note();
		if($this->manager) {

			$new_note = new Complaint_Note();
			$new_note->setComplaint( $this->complaint );

			
			/**
			 * @var \JetApplicationModule\Events\Complaint\MessageForCustomer\Main $event_handler
			 */
			$event_handler = $this->complaint->createEvent( Complaint::EVENT_MESSAGE_FOR_CUSTOMER )->getHandlerModule();
			$template = $event_handler->getEMailTemplates()[0];
			$template->setComplaint( $this->complaint );
			$generated_subject = $template->createEmail( $this->complaint->getEshop() )->getSubject();

			
			$this->manager->init(
				new_note: $new_note,
				
				generated_subject: $generated_subject,
				
				customer_email_address: $this->complaint->getEmail(),
				
				after_add: function( Complaint_Note $new_note, &$snippets ) : void {
					$this->complaint->newNote( $new_note );
					
					$snippets['complaint-history'] = $this->main_view->render('edit/history');
					$snippets['sent-emails'] = $this->main_view->render('edit/sent-emails');
					
				}
			);
			
			
			foreach( Handler_Note_MessageGenerator::initGenerators( $this->view, $this->complaint ) as $generator ) {
				$this->manager->addMessageGenerator( $generator );
			}
		}
		
	}
	
	public function handleOnlyIfComplaintIsEditable() : bool
	{
		return false;
	}
	
	public function renderDialog() : string
	{
		if(!$this->manager) {
			return '';
		}
		
		return $this->manager->showDialog();
	}
	
	public function renderButton() : string
	{
		if(!$this->manager) {
			return '';
		}
		
		return parent::renderButton();
	}
	
	public function handle(): void
	{
		if(!$this->manager) {
			return;
		}
		$this->manager->handle();
	}
}