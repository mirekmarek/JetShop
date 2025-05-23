<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;



use Jet\UI_messages;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Product;



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
			UI_messages::success(
				Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $product->getAdminTitle() ] )
			);
			
			Http_Headers::reload([], ['remove_item']);
		}
		
		$this->view->setVar('item', $product);
		$this->output( 'edit/set' );
	}
	

}