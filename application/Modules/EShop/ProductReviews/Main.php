<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ProductReviews;


use Jet\Application_Module;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Translator;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Managers_ProductReviews;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;


class Main extends Application_Module implements EShop_Managers_ProductReviews, EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public function renderRank( Product_EShopData $product ): string
	{
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
		return Translator::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = $this->getView();
				
				$view->setVar('product', $product);
				
				return $view->render('product-reviews');
				
			});
	}
	
	protected CustomerReviewManager $cs_manager;
	
	public function handleCustomerSectionReviews(): void
	{
		Translator::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() {
				$this->cs_manager = new CustomerReviewManager();
				
				if( ($write_review_p_id=Http_Request::GET()->getInt('write_review')) ) {
					if(!in_array($write_review_p_id, $this->cs_manager->getPossibleProductIds())) {
						Http_Headers::reload(unset_GET_params: ['write_review']);
					}
					
					$this->cs_manager->setWriteReviewProductId( $write_review_p_id );
					
					if($this->cs_manager->catchWriteReviewForm()) {
						Http_Headers::reload(unset_GET_params: ['write_review']);
					}
				}
			});
		
		
	}
	
	public function renderCustomerSectionReviews(): string
	{
		return Translator::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() {
				$view = $this->getView();
				$view->setVar('manager', $this->cs_manager);
				
				if($this->cs_manager->getWriteReviewProductId()) {
					return $view->render('customer-section/write_review');
				} else {
					return $view->render('customer-section');
				}
			});
		
		
	}
}