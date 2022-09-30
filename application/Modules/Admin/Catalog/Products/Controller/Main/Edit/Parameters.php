<?php
namespace JetShopModule\Admin\Catalog\Products;


use Jet\Logger;
use Jet\UI_messages;

use Jet\Http_Headers;
use Jet\Tr;

/**
 *
 */
trait Controller_Main_Edit_Parameters
{
	public function edit_parameters_Action() : void
	{
		$this->_setBreadcrumbNavigation();
		
		$product = static::getCurrentProduct();
		
		if( $product->catchParametersEditForm() ) {
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
		
		$this->output( 'edit/parameters' );
	}
}