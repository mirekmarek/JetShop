<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\MarketplaceIntegration_Module_Controller_ProductSettings;
use JetApplication\Exports_Module_Controller_ProductSettings;

abstract class Core_Admin_Managers_Product extends Admin_EntityManager_Module
{
	abstract public function renderSelectWidget( string $on_select,
	                                    int $selected_product_id=0,
	                                    ?array $only_type_filter=null,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_product' ) : string;
	
	abstract public function renderMarketPlaceSettings_main(
		MarketplaceIntegration_Module_Controller_ProductSettings $controller
	) : string;
	
	
	abstract public function renderMarketPlaceSettings_parameters(
		MarketplaceIntegration_Module_Controller_ProductSettings $controller
	) : string;
	
	abstract public function renderExportSettings_parameters(
		Exports_Module_Controller_ProductSettings $controller
	) : string;
	
}