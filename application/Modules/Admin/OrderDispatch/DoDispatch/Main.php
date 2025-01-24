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
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\Admin_Managers_OrderDispatch;
use JetApplication\Context;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\OrderDispatch;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_OrderDispatch
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'do-dispatch';
	
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
	
	public function showRecipient( OrderDispatch $dispatch ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('dispatch', $dispatch);
		
		return $view->render('recipient');
	}
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface
	{
		return new OrderDispatch();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Order dispatch';
	}
}