<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\Brands;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Brand;
use JetApplication\EShopEntity_Basic;
use JetApplication\MarketplaceIntegration_Module_Controller_BrandSettings;
use JetApplication\Brand;

class Main extends Admin_Managers_Brand
{
	public const ADMIN_MAIN_PAGE = 'brands';
	
	public static function getEntityInstance(): EShopEntity_Basic
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
			selected_entity_edit_URL: $selected?->getEditUrl()
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