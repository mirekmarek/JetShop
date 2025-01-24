<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\Manager_MetaInfo;
use JetApplication\MarketplaceIntegration_Module_Controller_BrandSettings;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - Brands',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Core_Admin_Managers_Brand extends Admin_EntityManager_WithEShopData_Interface
{
	public function renderSelectWidget( string $on_select,
	                                    int $selected_brand_id=0,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_brand' ) : string;
	
	public function renderMarketPlaceIntegrationForm(
		MarketplaceIntegration_Module_Controller_BrandSettings $controller
	) : string;
	
	public function renderMarketPlaceIntegrationBrands(
		MarketplaceIntegration_Module_Controller_BrandSettings $controller,
		string $dialog_selected_brand
	) : string;
	
}