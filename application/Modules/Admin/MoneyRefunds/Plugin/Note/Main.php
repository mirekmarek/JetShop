<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;

use JetApplication\Admin_EntityManager_EditorPlugin_Note;
use JetApplication\EShopEntity_Note;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event_MessageForCustomer;
use JetApplication\MoneyRefund_Note;

class Plugin_Note_Main extends Plugin
{
	public const KEY = 'note';
	
	use Admin_EntityManager_EditorPlugin_Note;
	
	protected function newNoteCreator() : EShopEntity_Note
	{
		/**
		 * @var MoneyRefund $item
		 */
		$item = $this->item;
		
		$new_note = new MoneyRefund_Note();
		$new_note->setMoneyRefund( $item );
		return $new_note;
	}
	
	protected function messageSubjectGenerator() : string
	{
		/**
		 * @var \JetApplicationModule\Events\MoneyRefund\MessageForCustomer\Main $event_handler
		 * @var MoneyRefund $item
		 */
		
		$item = $this->item;
		$event_handler = $item->createEvent( MoneyRefund_Event_MessageForCustomer::new() )->getHandlerModule();
		$template = $event_handler->getEMailTemplates()[0];
		$template->setMoneyRefund( $item );
		
		return $template->createEmail( $item->getEshop() )?->getSubject()??'';
		
	}
	
	protected function afterMessageAdded( EShopEntity_Note|MoneyRefund_Note $new_note ) : void
	{
		/**
		 * @var MoneyRefund $item
		 */
		$item = $this->item;
		
		$item->newNote( $new_note );
	}
}