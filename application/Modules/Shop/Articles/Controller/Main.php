<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Articles;

use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Layout;
use JetApplication\Content_Article_ShopData;
use JetApplication\Shops;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	protected Content_Article_ShopData $article;
	
	public function resolve(): bool|string
	{
		$main_router = MVC::getRouter();
		$path = $main_router->getUrlPath();
		if(!$path) {
			return false;
		}
		
		$article = Content_Article_ShopData::getByURLPathPart( $path, Shops::getCurrent() );
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
		$layout = MVC_Layout::getCurrentLayout();
		//$layout->setScriptName('landing-page');
		
		$layout->setVar('title', $this->article->getTitle() );
		
		$this->view->setVar('article', $this->article);
		
		$this->output('default');
	}
}