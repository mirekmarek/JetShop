<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\EShop\Articles;


use Jet\Application_Module;
use JetApplication\Content_Article_EShopData;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Managers_Articles;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;


class Main extends Application_Module implements EShop_Managers_Articles, EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public function renderProductAdvice( Product_EShopData $product ): string
	{
		$view = $this->getView();
		
		$articles = Content_Article_EShopData::getArticleList(
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