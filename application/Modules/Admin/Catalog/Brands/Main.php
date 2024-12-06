<?php
namespace JetApplicationModule\Admin\Catalog\Brands;

use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Entity_WithEShopData;
use JetApplication\Admin_Managers_Brand;
use JetApplication\MarketplaceIntegration_Module_Controller_BrandSettings;
use JetApplication\Brand;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopData_Interface, Admin_Managers_Brand
{
	use Admin_EntityManager_WithEShopData_Trait;

	public const ADMIN_MAIN_PAGE = 'brands';

	public const ACTION_GET = 'get_brand';
	public const ACTION_ADD = 'add_brand';
	public const ACTION_UPDATE = 'update_brand';
	public const ACTION_DELETE = 'delete_brand';
	
	public static function getEntityNameReadable() : string
	{
		return 'brand';
	}
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new Brand();
	}
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_brand_id=0,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_brand' ) : string
	{
		
		$selected = $selected_brand_id ? Brand::get($selected_brand_id) : null;
		
		return Admin_Managers::UI()->renderSelectEntityWidget(
			name: $name,
			caption: Tr::_('... select brand ...', dictionary: $this->module_manifest->getName()),
			on_select: $on_select,
			entity_type: Brand::getEntityType(),
			object_type_filter: null,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getInternalName(),
			selected_entity_edit_URL: $selected?->getEditURL()
		);
	}
	
	
	public function renderMarketPlaceIntegrationForm( MarketplaceIntegration_Module_Controller_BrandSettings $controller ): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		
		return $view->render('edit/marketplace/default');
	}
	
	public function renderMarketPlaceIntegrationBrands( MarketplaceIntegration_Module_Controller_BrandSettings $controller, string $dialog_selected_brand ): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		$view->setVar('dialog_selected_brand', $dialog_selected_brand);
		
		return $view->render('edit/marketplace/dialog/brands');
	}
}