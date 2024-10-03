<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\OrderDispatch\DoDispatch;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\MVC;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_OrderDispatch;
use JetApplication\Context;
use JetApplication\OrderDispatch;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_OrderDispatch
{
	public const PAGE_ID = 'do-dispatch';
	
	
	public function showDispatches( Context $context ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$dispatches = OrderDispatch::getListByContext( $context );
		
		$view->setVar('dispatches', $dispatches);
		
		return $view->render('dispatches');
	}
	
	public function showOrderDispatchStatus( OrderDispatch $dispatch ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('dispatch', $dispatch);
		
		return $view->render('status');
		
	}
	
	public function getOrderDispatchURL( int $id ): string
	{
		return MVC::getPage( Main::PAGE_ID )->getURL( GET_params: ['id'=>$id] );
	}
	
	public function showRecipient( OrderDispatch $dispatch ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('dispatch', $dispatch);
		
		return $view->render('recipient');
	}
	
	public function showName( int $id ): string
	{
		$entity = new class extends OrderDispatch implements Admin_Entity_Common_Interface {
			public function setEditable( bool $editable ): void
			{
			
			}
			
			public function getAdminTitle() : string
			{
				return $this->getNumber();
			}
			
			public function getEditURL(): string
			{
				return MVC::getPage( Main::PAGE_ID )->getURL( GET_params: ['id'=>$this->id] );
			}
			
			public function getAddForm(): Form
			{
				return new Form('', []);
			}
			
			public function catchAddForm(): bool
			{
				return false;
			}
			
			public function getEditForm(): Form
			{
				return new Form('', []);
			}
			
			public function catchEditForm(): bool
			{
				return false;
			}
		};
		
		return Admin_Managers::EntityEdit_Common()->renderShowName( $id, $entity );
	}
}