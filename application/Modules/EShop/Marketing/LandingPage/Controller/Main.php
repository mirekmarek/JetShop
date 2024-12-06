<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\EShop\Marketing\LandingPage;

use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Layout;
use JetApplication\Marketing_LandingPage;
use JetApplication\EShops;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	
	protected Marketing_LandingPage $lp;

	public function resolve(): bool|string
	{
		$main_router = MVC::getRouter();
		$path = $main_router->getUrlPath();
		if(!$path) {
			return false;
		}
		
		$lp = Marketing_LandingPage::getByURLPathPart( $path );
		if(!$lp) {
			return false;
		}
		
		if($lp->getEshop()->getKey()!==EShops::getCurrent()->getKey()) {
			return false;
		}
		
		if(
			!$lp->isActive() &&
			!$lp->checkPreviewKey()
		) {
			return false;
		}
		
		if($lp->getURLPathPart()!=$path) {
			$main_router->setIsRedirect( $lp->getURL() );
			return false;
		}
		
		$this->lp = $lp;
		
		$main_router->setUsedUrlPath( $path );
		
		return true;
	}
	
	/**
	 *
	 */
	public function default_Action() : void
	{
		$layout = MVC_Layout::getCurrentLayout();
		$layout->setScriptName('landing-page');
		
		$layout->setVar('title', $this->lp->getLandingPageTitle() );
		
		$this->view->setVar('lp', $this->lp);
		$this->output('default');
	}
}