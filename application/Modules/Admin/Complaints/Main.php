<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\Application_Module;
use Jet\Factory_MVC;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\Admin_Managers_Complaint;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\Order;
use JetApplication\Complaint;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopRelation_Interface, Admin_Managers_Complaint
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'complaints';

	public const ACTION_GET = 'get_complaint';
	public const ACTION_UPDATE = 'update_complaint';
	
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface
	{
		return new Complaint();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Complaint';
	}
	
	public function showComplaintStatus( Complaint $complaint ): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('complaint', $complaint);
		
		return $view->render('complaint_status');
	}
	
	public function showOrderComplaints( Order $order ) : void
	{
		$complaints= Complaint::getByOrder( $order );
		if(!$complaints) {
			return;
		}
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('complaints', $complaints);
		
		echo $view->render('order-complaints');
	}

}