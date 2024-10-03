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
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Trait;
use JetApplication\Admin_Managers_Complaint;
use JetApplication\EMail_TemplateProvider;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Order;
use JetApplication\Complaint as Application_Complaint;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithShopRelation_Interface, Admin_Managers_Complaint, EMail_TemplateProvider
{
	use Admin_EntityManager_WithShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'complaints';

	public const ACTION_GET = 'get_complaint';
	public const ACTION_UPDATE = 'update_complaint';
	
	public static function showActiveState( int $id ): string
	{
		return '';
	}
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): Entity_WithShopRelation|Admin_Entity_WithShopRelation_Interface
	{
		return new Complaint();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Complaint';
	}
	
	public function showComplaintStatus( Application_Complaint $complaint ): string
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
	
	public function getEMailTemplates(): array
	{
		$message = new Handler_Note_EMailTemplate();
		
		return [
			$message
		];
	}
	
}