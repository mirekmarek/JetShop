<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\MarketplaceIntegration_Module_Controller_KindOfProductSettings;
use JetApplication\Exports_Module_Controller_KindOfProductSettings;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - Kind of products',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_KindOfProduct extends Admin_EntityManager_Module
{
	
	abstract public function renderSelectWidget( string $on_select,
	                                    int $selected_kind_of_product_id=0,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_kind_of_product' ) : string;
	
	abstract public function renderMarketPlaceIntegrationForm(
		MarketplaceIntegration_Module_Controller_KindOfProductSettings $controller
	) : string;
	
	abstract public function renderMarketPlaceIntegrationCategories(
		MarketplaceIntegration_Module_Controller_KindOfProductSettings $controller,
		string $dialog_selected_category
	) : string;
	
	
	abstract public function renderExportsForm(
		Exports_Module_Controller_KindOfProductSettings $controller
	) : string;
	
	abstract public function renderExportsCategories(
		Exports_Module_Controller_KindOfProductSettings $controller,
		string $dialog_selected_category
	) : string;
	
}