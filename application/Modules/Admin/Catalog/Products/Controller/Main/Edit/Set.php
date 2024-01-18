<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Logger;
use Jet\UI_messages;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;


/**
 *
 */
trait Controller_Main_Edit_Set
{
	
	
	public function edit_set_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Set') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		$updated = false;
		
		if($product->catchSetAddItemForm()) {
			$updated = true;
		}
		
		if($product->catchSetSetupForm()) {
			$updated = true;
		}
		
		$GET = Http_Request::GET();
		
		if($GET->getInt('remove_item')) {
			$product->removeSetItem($GET->getInt('remove_item'));
			$updated = true;
		}
		
		if($updated) {
			$product->save();
			
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
			
			Http_Headers::reload([], ['remove_item']);
		}
		
		$this->view->setVar('item', $product);
		$this->output( 'edit/set' );
	}
	

}