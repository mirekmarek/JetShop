<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Tr;

/**
 *
 */
trait Controller_Main_Edit_Categories
{
	
	public function edit_categories_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Categories') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		if($product->isVariant()) {
			$product = $product->getVariantMasterProduct();
		}
		
		$this->view->setVar('item', $this->current_item);
		$this->view->setVar('product', $product);
		
		$this->output( 'edit/categories' );
	}
	
}