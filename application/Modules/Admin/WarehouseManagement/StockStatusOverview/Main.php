<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;

use Jet\Application_Module;
use Jet\Auth;
use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Trait;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Product;
use JetApplication\Admin_Managers_WarehouseManagementOverview;
use JetApplication\WarehouseManagement_StockCard;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithShopRelation_Interface, Admin_Managers_WarehouseManagementOverview
{
	use Admin_EntityManager_WithShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'stock-status-overview';

	public const ACTION_GET = 'get_stock_card';
	public const ACTION_CHANGE_LOCATION = 'change_location';
	public const ACTION_CANCEL_OR_REACTIVATE = 'cancel_or_reactivate';
	public const ACTION_RECALCULATE = 'recalculate';
	
	public static function getCurrentUserCanChangeLocation() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_CHANGE_LOCATION );
	}
	
	public static function getCurrentUserCanCancelOrReactivate() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_CANCEL_OR_REACTIVATE );
	}
	
	public static function getCurrentUserCanRecalculate() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_RECALCULATE );
	}
	
	
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
		return new StockCard();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Warehouse stock card';
	}
	
	public static function getStockCardDetailURL( WarehouseManagement_StockCard $card ) : string
	{
		return self::getEditUrl( $card->getId() );
	}
	
	public function renderProductStockStatusInfo( Product $product ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('product', $product);
				
				return $view->render('product-stock-status-info');
			}
		);
	}
	
}