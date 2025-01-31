<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;



use Jet\UI_messages;

use Jet\Http_Headers;
use Jet\Tr;
use JetApplication\Product;


trait Controller_Main_Edit_Parameters
{
	public function edit_parameters_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Parameters') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		$this->view->setVar('item', $product);
		
		if( $product->catchParametersEditForm() ) {

			UI_messages::success(
				Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $product->getAdminTitle() ] )
			);
			
			Http_Headers::reload();
		}
		
		$this->output( 'edit/parameters' );
	}
}