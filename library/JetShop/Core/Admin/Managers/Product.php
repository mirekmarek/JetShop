<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\MarketplaceIntegration_Module_Controller_ProductSettings;
use JetApplication\Exports_Module_Controller_ProductSettings;

interface Core_Admin_Managers_Product extends Admin_EntityManager_WithEShopData_Interface
{
	/**
	 * @deprecated
	 *
	 * @param int $product_id
	 * @return string
	 */
	public function getProductEditURL( int $product_id ) : string;
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_product_id=0,
	                                    ?array $only_type_filter=null,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_product' ) : string;
	
	public function renderMarketPlaceSettings_main(
		MarketplaceIntegration_Module_Controller_ProductSettings $controller
	) : string;
	
	
	public function renderMarketPlaceSettings_parameters(
		MarketplaceIntegration_Module_Controller_ProductSettings $controller
	) : string;
	
	public function renderExportSettings_parameters(
		Exports_Module_Controller_ProductSettings $controller
	) : string;
	
}