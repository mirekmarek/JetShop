<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Logger;
use Jet\UI_messages;
use JetApplication\Shops;


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
			
			Logger::success(
				'product_updated.variant_created',
				'Product '.$product->getAdminTitle().' ('.$product->getId().') updated - variant created',
				$product->getId(),
				$product->getAdminTitle(),
				$product
			);
			
			UI_messages::success(
				Tr::_( 'New variant has been created' )
			);
			
			Http_Headers::reload();
		}
		
		
		
		$updated = false;
		if( $product->catchUpdateVariantsForm() ) {
			Logger::success(
				'product_updated',
				'Product '.$product->getAdminTitle().' ('.$product->getId().') updated',
				$product->getId(),
				$product->getAdminTitle(),
				$product
			);
			
			UI_messages::success(
				Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $product->getAdminTitle() ] )
			);
			
			Http_Headers::reload();
		}
		
		

		
		$GET = Http_Request::GET();
		
		if($GET->exists('activate_variant')) {
			$variant_id=$GET->getInt('activate_variant');
			$shop_key = $GET->getString('shop');
			
			$variant = $product->getVariants()[$variant_id]??null;
			$shop = Shops::get( $shop_key );
			if( $variant && $shop ) {
				$variant->getShopData($shop)->activate();
				$product->actualizeVariantMaster();
				
				Logger::success(
					event: 'entity_shop_data_activated:'.$variant->getEntityType(),
					event_message: 'Entity '.$variant->getEntityType().' \''.$this->current_item->getAdminTitle().'\' ('.$variant->getId().') shop data '.$shop->getKey().' activated',
					context_object_id: $variant->getId().':'.$shop->getKey(),
					context_object_name: $this->current_item->getInternalName().' ('.$shop->getShopName().')'
				);

			}
			
			Http_Headers::reload(unset_GET_params: ['activate_variant', 'shop']);
		}
		
		if($GET->exists('deactivate_variant')) {
			$variant_id=$GET->getInt('deactivate_variant');
			$shop_key = $GET->getString('shop');
			
			$variant = $product->getVariants()[$variant_id]??null;
			$shop = Shops::get( $shop_key );
			if( $variant && $shop ) {
				$variant->getShopData($shop)->deactivate();
				$product->actualizeVariantMaster();
				
				Logger::success(
					event: 'entity_shop_data_deactivated:'.$variant->getEntityType(),
					event_message: 'Entity '.$variant->getEntityType().' \''.$this->current_item->getAdminTitle().'\' ('.$variant->getId().') shop data '.$shop->getKey().' deactivated',
					context_object_id: $variant->getId().':'.$shop->getKey(),
					context_object_name: $this->current_item->getInternalName().' ('.$shop->getShopName().')'
				);
			}
			
			Http_Headers::reload(unset_GET_params: ['deactivate_variant', 'shop']);
		}
		
		
		$this->view->setVar('item', $product);
		$this->view->setVar('new_variant', $new_variant);
		
		
		$this->output( 'edit/variants' );
	}
	
}