<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\TODO;

use Jet\Data_DateTime;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\MVC_Layout;
use Jet\AJAX;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		MVC_Layout::getCurrentLayout()->setScriptName('dialog');
		
		$GET = Http_Request::GET();
		
		$entity_type = $GET->getString('entity_type');
		$entity_id = $GET->getString('entity_id');
		
		$this->view->setVar('entity_type', $entity_type);
		$this->view->setVar('entity_id', $entity_id);
		
		if($GET->getBool('reload_button')) {
			AJAX::snippetResponse(
				$this->view->render('entity-edit/button')
			);
		}
		
		
		
		if(($done_id=$GET->getInt('done'))) {
			$items = Item::getItems( $entity_type, $entity_id );
			
			foreach( $items as $item ) {
				if($item->getId()==$done_id) {
					$item->done();
					break;
				}
			}
			
			AJAX::snippetResponse(
				$this->view->render('todo/items')
			);
		}
		
		
		$new_item = Item::prepareNew( $entity_type, $entity_id );
		$add_form = $new_item->getAddForm();
		$this->view->setVar('add_form', $add_form);
		
		if($add_form->catchInput()) {
			if($add_form->validate()) {
				$add_form->catchFieldValues();
				$new_item->setCreatedDateTime( Data_DateTime::now() );
				$new_item->save();
				
				$new_item = Item::prepareNew( $entity_type, $entity_id );
				$add_form = $new_item->getAddForm();
				$this->view->setVar('add_form', $add_form);
				
				AJAX::operationResponse( true, [
					'todo_add_form' => $this->view->render('todo/add_form'),
					'todo_items' => $this->view->render('todo/items'),
				] );

			} else {
				AJAX::operationResponse( false, [
					'todo_add_form' => $this->view->render('todo/add_form')
				] );
			}
		}
		
		if($edit_item_id=$GET->getInt('edit')) {
			$items = Item::getItems( $entity_type, $entity_id );
			
			foreach( $items as $item ) {
				if($item->getId()==$edit_item_id) {
					if($item->catchEditForm()) {
						$item->save();
					}
					break;
				}
			}
			
			AJAX::operationResponse( true, [
				'todo_items' => $this->view->render('todo/items'),
			] );
			
		}
		
		$this->output('todo');
		
	}
}