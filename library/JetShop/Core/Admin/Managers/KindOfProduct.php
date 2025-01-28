<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\MarketplaceIntegration_Module_Controller_KindOfProductSettings;
use JetApplication\Exports_Module_Controller_KindOfProductSettings;

interface Core_Admin_Managers_KindOfProduct extends Admin_EntityManager_Interface
{
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_kind_of_product_id=0,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_kind_of_product' ) : string;
	
	public function renderMarketPlaceIntegrationForm(
		MarketplaceIntegration_Module_Controller_KindOfProductSettings $controller
	) : string;
	
	public function renderMarketPlaceIntegrationCategories(
		MarketplaceIntegration_Module_Controller_KindOfProductSettings $controller,
		string $dialog_selected_category
	) : string;
	
	
	public function renderExportsForm(
		Exports_Module_Controller_KindOfProductSettings $controller
	) : string;
	
	public function renderExportsCategories(
		Exports_Module_Controller_KindOfProductSettings $controller,
		string $dialog_selected_category
	) : string;
	
}