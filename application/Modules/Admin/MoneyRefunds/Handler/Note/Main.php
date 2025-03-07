<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;


use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Note;
use JetApplication\MoneyRefund_Note;
use JetApplication\MoneyRefund;

class Handler_Note_Main extends Handler
{
	public const KEY = 'note';
	
	protected bool $has_dialog = true;

	protected ?Admin_Managers_Note $manager = null;
	
	protected function init() : void
	{
		
		$this->manager = Admin_Managers::Note();
		if($this->manager) {

			$new_note = new MoneyRefund_Note();
			$new_note->setMoneyRefund( $this->money_refund );

			
			/**
			 * @var \JetApplicationModule\Events\MoneyRefund\MessageForCustomer\Main $event_handler
			 */
			$event_handler = $this->money_refund->createEvent( MoneyRefund::EVENT_MESSAGE_FOR_CUSTOMER )->getHandlerModule();
			$template = $event_handler->getEMailTemplates()[0];
			$template->setMoneyRefund( $this->money_refund );
			$generated_subject = $template->createEmail( $this->money_refund->getEshop() )->getSubject();

			
			$this->manager->init(
				new_note: $new_note,
				
				generated_subject: $generated_subject,
				
				customer_email_address: $this->money_refund->getEmail(),
				
				after_add: function( MoneyRefund_Note $new_note, &$snippets ) : void {
					$this->money_refund->newNote( $new_note );
					
					$snippets['money-refund-history'] = $this->main_view->render('edit/history');
					$snippets['sent-emails'] = $this->main_view->render('edit/sent-emails');
					
				}
			);
			
		}
		
	}
	
	public function handleOnlyIfMoneyRefundIsEditable() : bool
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