<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;



use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\EShops;
use JetApplication\Product;


trait Controller_Main_Edit_Variants
{
	
	
	public function edit_variants_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Variants') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		if(!$product->isEditable()) {
			$this->view->setVar('item', $product);
			
			$this->output( 'edit/variants' );
			
			return;
		}
		
		$new_variant = $product->createNewVariantInstance();
		
		if( $product->catchAddVariantForm( $new_variant ) ) {
			
			UI_messages::success(
				Tr::_( 'New variant has been created' )
			);
			
			Http_Headers::reload();
		}
		
		
		
		$updated = false;
		if( $product->catchUpdateVariantsForm() ) {

			UI_messages::success(
				Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $product->getAdminTitle() ] )
			);
			
			Http_Headers::reload();
		}
		
		

		
		$GET = Http_Request::GET();
		
		if($GET->exists('activate_variant')) {
			$variant_id=$GET->getInt('activate_variant');
			
			if( ($variant = $product->getVariants()[$variant_id]??null) ) {
				$variant->activate();
				$product->actualizeVariantMaster();
			}
			
			Http_Headers::reload(unset_GET_params: ['activate_variant']);
		}
		
		if($GET->exists('activate_variant_completely')) {
			$variant_id=$GET->getInt('activate_variant_completely');
			
			if( ($variant = $product->getVariants()[$variant_id]??null) ) {
				$variant->activateCompletely();
				$product->actualizeVariantMaster();
			}
			
			Http_Headers::reload(unset_GET_params: ['activate_variant_completely']);
		}
		
		if($GET->exists('deactivate_variant')) {
			$variant_id=$GET->getInt('deactivate_variant');
			
			if( ($variant = $product->getVariants()[$variant_id]??null) ) {
				$variant->deactivate();
				$product->actualizeVariantMaster();
			}
			
			Http_Headers::reload(unset_GET_params: ['deactivate_variant']);
		}
		
		
		
		if($GET->exists('activate_variant_eshop_data')) {
			$variant_id=$GET->getInt('activate_variant_eshop_data');
			$eshop_key = $GET->getString('eshop');
			
			$variant = $product->getVariants()[$variant_id]??null;
			$eshop = EShops::get( $eshop_key );
			if( $variant && $eshop ) {
				$variant->activateEShopData( $eshop );
				$product->actualizeVariantMaster();
			}
			
			Http_Headers::reload(unset_GET_params: ['activate_variant_eshop_data', 'eshop']);
		}
		
		if($GET->exists('deactivate_variant_eshop_data')) {
			$variant_id=$GET->getInt('deactivate_variant_eshop_data');
			$eshop_key = $GET->getString('eshop');
			
			$variant = $product->getVariants()[$variant_id]??null;
			$eshop = EShops::get( $eshop_key );
			if( $variant && $eshop ) {
				$variant->deactivateEShopData( $eshop );
				$product->actualizeVariantMaster();
			}
			
			Http_Headers::reload(unset_GET_params: ['deactivate_variant_eshop_data', 'eshop']);
		}
		
		
		$this->view->setVar('item', $product);
		$this->view->setVar('new_variant', $new_variant);
		
		
		$this->output( 'edit/variants' );
	}
	
}