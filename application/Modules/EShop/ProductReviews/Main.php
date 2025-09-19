<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ProductReviews;

use Jet\Translator;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop_ProductReviews;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;


class Main extends Application_Service_EShop_ProductReviews implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	protected function getRelevantProduct(Product_EShopData $product ) : Product_EShopData
	{
		if($product->isVariant()) {
			return $product->getVariantMasterProduct() ? : $product;
		}
		return $product;
	}
	
	public function getReviewCount( Product_EShopData $product ): int
	{
		return $this->getRelevantProduct($product)->getReviewCount();
	}
	
	public function renderRankForListing( Product_EShopData $product ) : string
	{
		$product = $this->getRelevantProduct( $product );
		return Translator::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = $this->getView();
				
				$view->setVar('product', $product);
				
				return $view->render('product-rank-listing');
			});
		
	}
	
	public function renderRank( Product_EShopData $product ): string
	{
		$product = $this->getRelevantProduct( $product );
		return Translator::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = $this->getView();
				
				$view->setVar('product', $product);
				
				return $view->render('product-rank');
			});
	}
	
	public function renderReviews( Product_EShopData $product ): string
	{
		$product = $this->getRelevantProduct( $product );
		
		return Translator::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = $this->getView();
				
				$view->setVar('product', $product);
				
				return $view->render('product-reviews');
				
			});
	}
	
}