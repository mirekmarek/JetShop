<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Marketing\LandingPage;


use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Layout;
use JetApplication\Marketing_LandingPage;
use Jet\Navigation_Breadcrumb;


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
		
		if(
			!$lp->isActive() &&
			!$lp->checkPreviewKey()
		) {
			return false;
		}
		
		if( !$lp->checkURL( $path ) ) {
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
		
		Navigation_Breadcrumb::addURL( $this->lp->getLandingPageTitle() );
		
		$layout->setVar('title', $this->lp->getLandingPageTitle() );
		$layout->setVar('description', $this->lp->getLandingPageDescription() );
		
		$this->view->setVar('lp', $this->lp);
		$this->output('default');
	}
}