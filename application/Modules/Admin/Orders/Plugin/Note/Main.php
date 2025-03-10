<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use JetApplication\Admin_EntityManager_EditorPlugin_Note;
use JetApplication\EShopEntity_Note;
use JetApplication\EShopEntity_Note_MessageGenerator;
use JetApplication\Order_Event_MessageForCustomer;
use JetApplication\Order_Note;
use JetApplication\Order;

class Plugin_Note_Main extends Plugin
{
	public const KEY = 'note';
	
	use Admin_EntityManager_EditorPlugin_Note;
	
	protected function newNoteCreator() : Order_Note
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		$new_note = new Order_Note();
		$new_note->setOrder( $item );
		
		return $new_note;
	}
	
	protected function messageSubjectGenerator() : string
	{
		/**
		 * @var \JetApplicationModule\Events\Order\MessageForCustomer\Main $event_handler
		 * @var Order $item
		 */
		
		$item = $this->item;
		$event_handler = $this->item->createEvent( Order_Event_MessageForCustomer::new() )->getHandlerModule();
		$template = $event_handler->getEMailTemplates()[0];
		$template->setOrder( $item );
		
		return $template->createEmail( $item->getEshop() )->getSubject();
	}
	
	protected function afterMessageAdded( EShopEntity_Note|Order_Note $new_note ) : void
	{
		/**
		 * @var Order $item
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