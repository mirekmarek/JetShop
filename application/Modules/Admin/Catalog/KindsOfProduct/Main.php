<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\KindsOfProduct;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_KindOfProduct;
use JetApplication\EShopEntity_Basic;
use JetApplication\Exports_Module_Controller_KindOfProductSettings;
use JetApplication\MarketplaceIntegration_Module_Controller_KindOfProductSettings;
use JetApplication\KindOfProduct;


class Main extends Admin_Managers_KindOfProduct
{
	public const ADMIN_MAIN_PAGE = 'kind-of-product';
	
	public function renderSelectWidget( string $on_select,
                                        int $selected_kind_of_product_id=0,
                                        ?bool $only_active_filter=null,
                                        string $name='select_kind_of_product' ) : string
	{
		$selected = $selected_kind_of_product_id ? KindOfProduct::get($selected_kind_of_product_id) : null;
		
		return Admin_Managers::UI()->renderSelectEntityWidget(
			name: $name,
			caption: Tr::_('... select kind of product ...', dictionary: $this->module_manifest->getName()),
			on_select: $on_select,
			entity_type: KindOfProduct::getEntityType(),
			object_type_filter: null,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getInternalName(),
			selected_entity_edit_URL: $selected?->getEditUrl()
		);
	}
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new KindOfProduct();
	}
	
	public function renderMarketPlaceIntegrationForm(
		MarketplaceIntegration_Module_Controller_KindOfProductSettings $controller
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		
		return $view->render('edit/marketplace/default');
	}
	
	public function renderMarketPlaceIntegrationCategories(
		MarketplaceIntegration_Module_Controller_KindOfProductSettings $controller,
		string $dialog_selected_category
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		$view->setVar('dialog_selected_category', $dialog_selected_category);
		
		return $view->render('edit/marketplace/dialog/categories');
	}
	
	
	public function renderExportsForm(
		Exports_Module_Controller_KindOfProductSettings $controller
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		
		return $view->render('edit/exports/default');
	}
	
	public function renderExportsCategories(
		Exports_Module_Controller_KindOfProductSettings $controller,
		string $dialog_selected_category
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setController( $controller );
		$view->setVar('dialog_selected_category', $dialog_selected_category);
		
		return $view->render('edit/exports/dialog/categories');
	}
	
}