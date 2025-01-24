<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;

use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Note;
use JetApplication\Order_Note;
use JetApplication\Order;

class Handler_Note_Main extends Handler
{
	public const KEY = 'note';
	
	protected bool $has_dialog = true;

	protected ?Admin_Managers_Note $manager = null;
	
	protected function init() : void
	{
		
		$this->manager = Admin_Managers::Note();
		if($this->manager) {

			$new_note = new Order_Note();
			$new_note->setOrder( $this->order );

			
			/**
			 * @var \JetApplicationModule\Events\Order\MessageForCustomer\Main $event_handler
			 */
			$event_handler = $this->order->createEvent( Order::EVENT_MESSAGE_FOR_CUSTOMER )->getHandlerModule();
			$template = $event_handler->getEMailTemplates()[0];
			$template->setOrder( $this->order );
			$generated_subject = $template->createEmail( $this->order->getEshop() )->getSubject();

			
			$this->manager->init(
				new_note: $new_note,
				
				generated_subject: $generated_subject,
				
				customer_email_address: $this->order->getEmail(),
				
				after_add: function( Order_Note $new_note, &$snippets ) : void {
					$this->order->newNote( $new_note );
					
					$snippets['order-history'] = $this->main_view->render('edit/history');
					$snippets['sent-emails'] = $this->main_view->render('edit/sent-emails');
					
				}
			);
			
			
			foreach( Handler_Note_MessageGenerator::initGenerators( $this->view, $this->order ) as $generator ) {
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