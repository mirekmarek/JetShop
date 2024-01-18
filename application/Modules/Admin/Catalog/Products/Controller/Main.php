<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Http_Request;
use Jet\Tr;

use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Manager_Controller;
use JetApplication\Delivery_Class;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Controller_Main extends Admin_Entity_WithShopData_Manager_Controller
{
	use Controller_Main_Edit_Images;
	use Controller_Main_Edit_Parameters;
	use Controller_Main_Edit_Categories;
	use Controller_Main_Edit_Variants;
	use Controller_Main_Edit_Set;
	
	
	
	protected function newItemFactory(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		/**
		 * @var Product $new_item
		 */
		$new_item = parent::newItemFactory();
		
		$type = Http_Request::GET()->getString('type', '', [
			Product::PRODUCT_TYPE_REGULAR,
			Product::PRODUCT_TYPE_VARIANT_MASTER,
			Product::PRODUCT_TYPE_SET
		]);
		
		if(!$type) {
			die();
		}
		
		$new_item->setType( $type );
		
		$new_item->setDeliveryClassId( Delivery_Class::getDefault()->getId() );
		
		return $new_item;
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_parameters', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='parameters';
			} );
		
		$this->router->addAction('edit_images', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='images';
			} );
		
		$this->router->addAction('edit_categories', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='categories';
			} );
		
		$this->router->addAction('edit_variants', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='variants';
			} );
		
		$this->router->addAction('edit_set', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='set';
			} );
	}
	
	protected function getTabs(): array
	{
		$product = $this->current_item;
		
		$_tabs = [
			'main'             => Tr::_('Main data'),
			'images'           => Tr::_('Images'),
			'parameters'       => Tr::_('Parameters'),
			'categories'       => Tr::_('Categories'),
		];
		
		switch($product->getType()) {
			case Product::PRODUCT_TYPE_REGULAR:
			case Product::PRODUCT_TYPE_VARIANT:
				break;
				
			case Product::PRODUCT_TYPE_VARIANT_MASTER:
				$_tabs['variants'] = Tr::_('Variants');
				break;
			case Product::PRODUCT_TYPE_SET:
				$_tabs['set'] = Tr::_('Set');
				break;
		}
		

		return $_tabs;
	}
	
	
	public function setupListing() : void
	{
		
		$this->listing_manager->addColumn( new Listing_Column_Name() );
		$this->listing_manager->addColumn( new Listing_Column_Price() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'active_state',
			'name',
			'internal_notes',
			'price',
		]);
		
		$this->listing_manager->addFilter( new Listing_Filter_Categories() );
		$this->listing_manager->addFilter( new Listing_Filter_ProductKind() );
		$this->listing_manager->addFilter( new Listing_Filter_ProductType() );
		$this->listing_manager->addFilter( new Listing_Filter_Brand() );
		$this->listing_manager->addFilter( new Listing_Filter_Supplier() );
		
		$this->listing_manager->setCreateBtnRenderer( function() : string {
			return $this->view->render('create_buttons');
		} );
		
	}
	
	
}