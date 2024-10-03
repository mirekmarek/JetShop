<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Banners;

use Jet\Application_Module;
use JetApplication\Marketing_Banner;
use JetApplication\Shop_Managers_Banners;
use JetApplication\Shop_ModuleUsingTemplate_Trait;
use JetApplicationModule\Admin\Marketing\BannerGroups\BannerGroup;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_Banners
{
	use Shop_ModuleUsingTemplate_Trait;
	public function renderPosition( string $banner_group_code ) : string
	{
		
		$group = BannerGroup::getByCode( $banner_group_code );
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