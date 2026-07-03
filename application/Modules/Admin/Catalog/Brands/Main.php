<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Brands;


use Jet\Factory_MVC;
use JetApplication\Application_Service_Admin_Brand;
use JetApplication\EShopEntity_Basic;
use JetApplication\MarketplaceIntegration_Module_Controller_BrandSettings;
use JetApplication\Brand;

class Main extends Application_Service_Admin_Brand
{
	public const ADMIN_MAIN_PAGE = 'brands';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Brand();
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