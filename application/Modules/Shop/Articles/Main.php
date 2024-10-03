<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Articles;

use Jet\Application_Module;
use JetApplication\Content_Article_ShopData;
use JetApplication\Product_ShopData;
use JetApplication\Shop_Managers_Articles;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_Articles, Shop_ModuleUsingTemplate_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
	
	public function renderProductAdvice( Product_ShopData $product ): string
	{
		$view = $this->getView();
		
		$articles = Content_Article_ShopData::getArticleList(
			kind_code: 'advice',
			category_ids: $product->getCategoryIds()
		);
		
		if(!$articles) {
			return '';
		}
		
		$view->setVar('articles', $articles);
		
		return $view->render('product-advice');
	}
}