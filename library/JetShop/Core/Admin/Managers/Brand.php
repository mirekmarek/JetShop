<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\MarketplaceIntegration_Module_Controller_BrandSettings;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - Brands',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_Brand extends Admin_EntityManager_Module
{
	abstract public function renderSelectWidget( string $on_select,
	                                    int $selected_brand_id=0,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_brand' ) : string;
	
	abstract public function renderMarketPlaceIntegrationForm(
		MarketplaceIntegration_Module_Controller_BrandSettings $controller
	) : string;
	
	abstract public function renderMarketPlaceIntegrationBrands(
		MarketplaceIntegration_Module_Controller_BrandSettings $controller,
		string $dialog_selected_brand
	) : string;
	
}