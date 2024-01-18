<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Tr;
use JetApplication\Admin_Managers;

/**
 *
 */
trait Controller_Main_Edit_Images
{
	
	public function edit_images_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Images') );
		
		$this->view->setVar('item', $this->current_item);
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		$manager = Admin_Managers::Image();
		
		$manager->setEditable( Main::getCurrentUserCanEdit() );
		
		$manager->handleProductImageManagement( $product );
		
		$this->output( 'edit/images' );
		
	}
}