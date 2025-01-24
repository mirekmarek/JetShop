<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\OrderPersonalReceipt\DoPersonalReceipt;

use Jet\Application_Module;
use Jet\Factory_MVC;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\Admin_Managers_OrderPersonalReceipt;
use JetApplication\Context;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\OrderPersonalReceipt;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_OrderPersonalReceipt
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'do-personal-receipt';
	
	
	public function showDispatches( Context $context ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$dispatches = OrderPersonalReceipt::getListByContext( $context );
		
		$view->setVar('dispatches', $dispatches);
		
		return $view->render('dispatches');
	}
	
	public function showOrderPersonalReceiptStatus( OrderPersonalReceipt $dispatch ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('dispatch', $dispatch);
		
		return $view->render('status');
		
	}
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface
	{
		return new OrderPersonalReceipt();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Order Personal Receipt dispatch';
	}
	
}