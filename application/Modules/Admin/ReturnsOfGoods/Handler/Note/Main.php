<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;


use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Note;
use JetApplication\ReturnOfGoods_Note;
use JetApplication\ReturnOfGoods;

class Handler_Note_Main extends Handler
{
	public const KEY = 'note';
	
	protected bool $has_dialog = true;

	protected ?Admin_Managers_Note $manager = null;
	
	protected function init() : void
	{
		
		$this->manager = Admin_Managers::Note();
		if($this->manager) {
			$new_note = new ReturnOfGoods_Note();
			$new_note->setReturnOfGoods( $this->return_of_goods );

			
			/**
			 * @var \JetApplicationModule\Events\ReturnOfGoods\MessageForCustomer\Main $event_handler
			 */
			$event_handler = $this->return_of_goods->createEvent( ReturnOfGoods::EVENT_MESSAGE_FOR_CUSTOMER )->getHandlerModule();
			$template = $event_handler->getEMailTemplates()[0];
			$template->setReturnOfGoods( $this->return_of_goods );
			$generated_subject = $template->createEmail( $this->return_of_goods->getEshop() )->getSubject();

			
			$this->manager->init(
				new_note: $new_note,
				
				generated_subject: $generated_subject,
				
				customer_email_address: $this->return_of_goods->getEmail(),
				
				after_add: function( ReturnOfGoods_Note $new_note, &$snippets ) : void {
					$this->return_of_goods->newNote( $new_note );
					
					$snippets['return-of-goods-history'] = $this->main_view->render('edit/history');
					$snippets['sent-emails'] = $this->main_view->render('edit/sent-emails');
					
				}
			);
			
			
			foreach( Handler_Note_MessageGenerator::initGenerators( $this->view, $this->return_of_goods ) as $generator ) {
				$this->manager->addMessageGenerator( $generator );
			}
		}
		
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
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