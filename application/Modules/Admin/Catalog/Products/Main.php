<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Application_Service_Admin_Product;
use JetApplication\EShopEntity_Basic;
use JetApplication\Product;
use JetApplication\Application_Service_Admin;
use JetApplication\Exports_Module_Controller_ProductSettings;
use JetApplication\MarketplaceIntegration_Module_Controller_ProductSettings;


class Main extends Application_Service_Admin_Product
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
		
		return Application_Service_Admin::UI()->renderSelectEntityWidget(
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
		return static::getCurrentUserCanDoAction( static::ACTION_SET_PRICE );
	}
	
	public static function getCurrentUserCanSetAvailability() : bool
	{
		return static::getCurrentUserCanDoAction( static::ACTION_SET_AVAILABILITY );
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