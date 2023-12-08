<?php
namespace JetApplicationModule\Admin\Catalog\Products;


/**
 *
 */
trait Controller_Main_Edit_Categories
{
	
	public function edit_categories_Action() : void
	{
		$this->_setBreadcrumbNavigation();
		
		$product = static::getCurrentProduct();
		
		
		$this->output( 'edit/categories' );
	}
	
}