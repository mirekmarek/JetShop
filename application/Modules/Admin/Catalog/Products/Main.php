<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\Products;


use Jet\Auth;
use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Managers_Product;
use JetApplication\EShopEntity_Basic;
use JetApplication\Product;
use JetApplication\Admin_Managers;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Exports_Module_Controller_ProductSettings;
use JetApplication\MarketplaceIntegration_Module_Controller_ProductSettings;


class Main extends Admin_Managers_Product
{
	public const ADMIN_MAIN_PAGE = 'products';

	public const ACTION_SET_PRICE = 'set_price';
	public const ACTION_SET_AVAILABILITY = 'set_availability';
	
	
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
			selected_entity_edit_URL: $selected?->getEditUrl()
		);
	}
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Product();
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