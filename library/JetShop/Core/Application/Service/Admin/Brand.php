<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_Admin;
use JetApplication\MarketplaceIntegration_Module_Controller_BrandSettings;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'Catalog - Brands',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_Brand extends Admin_EntityManager_Module
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