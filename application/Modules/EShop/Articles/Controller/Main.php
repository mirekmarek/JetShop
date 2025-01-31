<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\EShop\Articles;


use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Layout;
use Jet\Navigation_Breadcrumb;
use JetApplication\Content_Article_EShopData;
use JetApplication\EShops;


class Controller_Main extends MVC_Controller_Default
{

	protected Content_Article_EShopData $article;
	
	public function resolve(): bool|string
	{
		$main_router = MVC::getRouter();
		$path = $main_router->getUrlPath();
		if(!$path) {
			return false;
		}
		
		$article = Content_Article_EShopData::getByURLPathPart( $path, EShops::getCurrent() );
		if(!$article) {
			return false;
		}
		
		if(
			!$article->isActive() &&
			!$article->checkPreviewKey()
		) {
			return false;
		}
		
		if($article->getURLPathPart()!=$path) {
			$main_router->setIsRedirect( $article->getURL() );
			return false;
		}
		
		$this->article = $article;
		
		$main_router->setUsedUrlPath( $path );
		
		return true;
	}
	
	
	/**
	 *
	 */
	public function default_Action() : void
	{
		Navigation_Breadcrumb::addURL(
			$this->article->getTitle()
		);
		
		$layout = MVC_Layout::getCurrentLayout();
		
		$layout->setVar('title', $this->article->getTitle() );
		
		$this->view->setVar('article', $this->article);
		
		$this->output('default');
	}
}