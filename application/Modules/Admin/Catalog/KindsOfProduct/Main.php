<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\KindsOfProduct;


use Jet\Factory_MVC;
use JetApplication\Application_Service_Admin_KindOfProduct;
use JetApplication\EShopEntity_Basic;
use JetApplication\Exports_Module_Controller_KindOfProductSettings;
use JetApplication\MarketplaceIntegration_Module_Controller_KindOfProductSettings;
use JetApplication\KindOfProduct;


class Main extends Application_Service_Admin_KindOfProduct
{
	public const ADMIN_MAIN_PAGE = 'kind-of-product';
	
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