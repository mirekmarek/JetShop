<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;


use JetApplication\Admin_EntityManager_EditorPlugin_Note;
use JetApplication\EShopEntity_Note;
use JetApplication\EShopEntity_Note_MessageGenerator;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event_MessageForCustomer;
use JetApplication\ReturnOfGoods_Note;

class Plugin_Note_Main extends Plugin
{
	public const KEY = 'note';
	
	use Admin_EntityManager_EditorPlugin_Note;
	
	protected function newNoteCreator() : ReturnOfGoods_Note
	{
		/**
		 * @var ReturnOfGoods $item
		 */
		$item = $this->item;
		$new_note = new ReturnOfGoods_Note();
		$new_note->setReturnOfGoods( $item );
		
		return $new_note;
	}
	
	protected function messageSubjectGenerator() : string
	{
		/**
		 * @var \JetApplicationModule\Events\ReturnOfGoods\MessageForCustomer\Main $event_handler
		 * @var ReturnOfGoods $item
		 */
		
		$item = $this->item;
		$event_handler = $this->item->createEvent( ReturnOfGoods_Event_MessageForCustomer::new() )->getHandlerModule();
		$template = $event_handler->getEMailTemplates()[0];
		$template->setReturnOfGoods( $item );
		
		return $template->createEmail( $item->getEshop() )->getSubject();
	}
	
	protected function afterMessageAdded( EShopEntity_Note|ReturnOfGoods_Note $new_note ) : void
	{
		/**
		 * @var ReturnOfGoods $item
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