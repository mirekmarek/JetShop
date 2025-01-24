<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Auth;
use Jet\Factory_MVC;
use Jet\MVC;
use Jet\Tr;
use Jet\Application_Module;
use JetApplication\Product;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Product;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Entity_WithEShopData;
use JetApplication\Exports_Module_Controller_ProductSettings;
use JetApplication\MarketplaceIntegration_Module_Controller_ProductSettings;


class Main extends Application_Module implements Admin_Managers_Product
{
	use Admin_EntityManager_WithEShopData_Trait;

	public const ADMIN_MAIN_PAGE = 'products';

	public const ACTION_GET = 'get_product';
	public const ACTION_ADD = 'add_product';
	public const ACTION_UPDATE = 'update_product';
	public const ACTION_DELETE = 'delete_product';
	public const ACTION_SET_PRICE = 'set_price';
	public const ACTION_SET_AVAILABILITY = 'set_availability';
	
	
	public function getProductEditURL( int $product_id ) : string
	{
		$page = MVC::getPage( static::ADMIN_MAIN_PAGE );
		
		$get_params['id'] = $product_id;
		
		return $page->getURL([], $get_params);
	}
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_product_id=0,
	                                    ?array $only_type_filter=null,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_product' ) : string
	{
		
		$selected = $selected_product_id ? Product::get($selected_product_id) : null;
		
		return Admin_Managers::UI()->renderSelectEntityWidget(
			name: $name,
			caption: Tr::_('... select product ...', dictionary: $this->module_manifest->getName()),
			on_select: $on_select,
			entity_type: Product::getEntityType(),
			object_type_filter: $only_type_filter,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getAdminTitle(),
			selected_entity_edit_URL: $selected?->getEditURL()
		);
	}
	
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new Product();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'product';
	}
	
	public static function getCurrentUserCanSetPrice() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_SET_PRICE );
	}
	
	public static function getCurrentUserCanSetAvailability() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_SET_AVAILABILITY );
	}
	
	public function renderMarketPlaceSettings_main(
		MarketplaceIntegration_Module_Controller_ProductSettings $controller
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		return $view->render('edit/marketplace/main');
	}
	
	
	public function renderMarketPlaceSettings_parameters(
		MarketplaceIntegration_Module_Controller_ProductSettings $controller
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		return $view->render('edit/marketplace/parameters');
	}
	
	public function renderExportSettings_parameters( Exports_Module_Controller_ProductSettings $controller ): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		return $view->render('edit/export/parameters');
	}
}