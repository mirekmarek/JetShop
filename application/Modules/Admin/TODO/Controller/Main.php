<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\TODO;

use Jet\Data_DateTime;
use Jet\Data_Text;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Router_Interface;
use Jet\AJAX;
use JetApplication\Auth_Administrator_User;


class Controller_Main extends MVC_Controller_Default
{

	protected string $entity_type;
	protected int $entity_id;
	
	
	public function getControllerRouter(): MVC_Controller_Router_Interface
	{
		$GET = Http_Request::GET();
		
		$this->entity_type = $GET->getString('entity_type');
		$this->entity_id = $GET->getString('entity_id');
		
		$this->view->setVar('entity_type', $this->entity_type);
		$this->view->setVar('entity_id', $this->entity_id);
		
		$router = new MVC_Controller_Router( $this );
		
		$router->addAction('edit')
			->setResolver( function() use ($GET) : bool {
				return $GET->getInt('edit')>0;
			} );
		
		
		$router->addAction('reload_button')
			->setResolver( function() use ($GET) : bool {
				return $GET->getBool('reload_button');
			} );
		
		$router->addAction('done')
			->setResolver( function() use ($GET) : bool {
				return $GET->getInt('done')>0;
			} );
		
		$router->addAction('add')
			->setResolver( function() use ($GET) : bool {
				return $GET->getBool('add');
			} );
		
		$router->addAction('whisper_user')
			->setResolver( function() use ($GET) : bool {
				return $GET->exists('whisper_user');
			} );
		
		$router->addAction('todo_add_delegate_actualize')
			->setResolver( function() use ($GET) : bool {
				return $GET->exists('todo_add_delegate_actualize');
			});
		
		$router->addAction('todo_edit_delegate_actualize')
			->setResolver( function() use ($GET) : bool {
				return $GET->exists('todo_edit_delegate_actualize');
			});
		
		
		
		$router->setDefaultAction('default');
		
		return $router;
	}
	
	public function todo_edit_delegate_actualize_Action() : void
	{
		$user_ids = Http_Request::GET()->getString('todo_edit_delegate_actualize');
		$user_ids = trim($user_ids, '|');
		$user_ids = explode('|', $user_ids);
		
		$users = Auth_Administrator_User::fetchInstances(['id'=>$user_ids]);
		
		$this->view->setVar('users', $users);
		
		
		$item_id = Http_Request::GET()->getInt('item_id');
		$item = Item::get($item_id);
		$this->view->setVar('item', $item);
		
		AJAX::snippetResponse( $this->view->render('todo/edit_form/delegated_users') );
	}
	
	public function todo_add_delegate_actualize_Action() : void
	{
		$user_ids = Http_Request::GET()->getString('todo_add_delegate_actualize');
		$user_ids = trim($user_ids, '|');
		$user_ids = explode('|', $user_ids);
		
		$users = Auth_Administrator_User::fetchInstances(['id'=>$user_ids]);
		
		$this->view->setVar('users', $users);
		
		AJAX::snippetResponse( $this->view->render('todo/add_form/delegated_users') );
	}
	
	public function whisper_user_Action() : void
	{
		$w = Http_Request::GET()->getString('whisper_user');
		
		$updateText = function( string $t ) : string {
			return strtolower( Data_Text::removeAccents( trim( $t ) ) );
		};
		
		if($w) {
			$w = $updateText( $w );
		}
		
		$_users = Auth_Administrator_User::fetchInstances();
		$users = [];
		foreach($_users as $user) {
			if($user->isBlocked()) {
				continue;
			}
			
			
			if($w) {
				$match = false;
				
				if(str_contains( $updateText($user->getEmail()), $w)) {
					$match = true;
				}
				
				if(str_contains( $updateText($user->getUsername()), $w)) {
					$match = true;
				}
				
				if(str_contains( $updateText($user->getFirstName().' '.$user->getSurname()), $w)) {
					$match = true;
				}
				
				if(str_contains( $updateText($user->getSurname().' '.$user->getFirstName()), $w)) {
					$match = true;
				}
				
				
				if(!$match) {
					continue;
				}
			}
			
			$users[] = $user;
		}
		
		$this->view->setVar('users', $users);
		AJAX::snippetResponse(
			$this->view->render('whisper_user')
		);
		
	}
	
	public function reload_button_Action() : void
	{
		AJAX::snippetResponse(
			$this->view->render('entity-edit/button')
		);
	}
	
	public function done_Action() : void
	{
		$done_id=Http_Request::GET()->getInt('done');

		$items = Item::getItems( $this->entity_type, $this->entity_id );
		
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
	
	public function edit_Action() : void
	{
		$edit_item_id=Http_Request::GET()->getInt('edit');
		
		$items = Item::getItems( $this->entity_type, $this->entity_id );
		
		foreach( $items as $item ) {
			if($item->getId()==$edit_item_id) {
				if($item->catchEditForm()) {
					$item->save();
				}
				break;
			}
		}
		
		$items = Item::getDelegatedItems( $this->entity_type, $this->entity_id );
		
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
			'todo_delegated_items' => $this->view->render('todo/delegated_items'),
		] );
		
	}
	
	public function add_Action() : void
	{
		$new_item = Item::prepareNew( $this->entity_type, $this->entity_id );
		$add_form = $new_item->getAddForm();
		$this->view->setVar('new_item', $new_item);
		$this->view->setVar('add_form', $add_form);
		
		if($add_form->catchInput()) {
			if($add_form->validate()) {
				$add_form->catchFieldValues();
				$new_item->setCreatedDateTime( Data_DateTime::now() );
				$new_item->save();
				
				$new_item = Item::prepareNew( $this->entity_type, $this->entity_id );
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
		
	}

	public function default_Action() : void
	{
		$new_item = Item::prepareNew( $this->entity_type, $this->entity_id );
		$add_form = $new_item->getAddForm();
		$this->view->setVar('new_item', $new_item);
		$this->view->setVar('add_form', $add_form);
		
		$this->output('todo');
		
	}
}