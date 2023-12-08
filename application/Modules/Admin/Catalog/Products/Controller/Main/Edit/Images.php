<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use JetApplication\Admin_Managers;

/**
 *
 */
trait Controller_Main_Edit_Images
{
	
	public function edit_images_Action() : void
	{
		$this->_setBreadcrumbNavigation();
		
		$product = static::getCurrentProduct();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( Main::getCurrentUserCanEdit() );
		
		$manager->handleProductImageManagement( $product );
		
		$this->output( 'edit/images' );
		
	}
}