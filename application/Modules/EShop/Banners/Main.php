<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Banners;


use Jet\Application_Module;
use JetApplication\Marketing_Banner;
use JetApplication\Marketing_BannerGroup;
use JetApplication\EShop_Managers_Banners;
use JetApplication\EShop_ModuleUsingTemplate_Trait;


class Main extends Application_Module implements EShop_Managers_Banners
{
	use EShop_ModuleUsingTemplate_Trait;
	public function renderPosition( string $banner_group_code ) : string
	{
		
		$group = Marketing_BannerGroup::getByCode( $banner_group_code );
		if(!$group) {
			return '';
		}
		
		$banners = Marketing_Banner::getActiveByGroup( $group );
		
		$view = $this->getView();
		$view->setVar('group', $group);
		$view->setVar('banners', $banners);
		
		
		return $view->render('default');
	}
}