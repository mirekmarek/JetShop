<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\DeliveryNotes;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\Admin_Managers_DeliveryNote;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\Order;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopRelation_Interface, Admin_Managers_DeliveryNote
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'delivery-notes';
	
	public const ACTION_GET = 'get_invoice';
	public const ACTION_UPDATE = 'update_invoice';
	
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
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface
	{
		return new DeliveryNote();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Invoice';
	}
	
	public function showOrderInvoices( Order $order ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($order) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar( 'invoices', DeliveryNote::getListByOrder( $order ) );
				
				return $view->render('order-invoices');
			}
		);
	}
	
}