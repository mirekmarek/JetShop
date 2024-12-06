<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Closure;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Locale;
use Jet\Tr;

use Jet\UI_messages;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Controller;
use JetApplication\Admin_Managers;
use JetApplication\Availabilities;
use JetApplication\Delivery_Class;
use JetApplication\Entity_WithEShopData;
use JetApplication\Exports;
use JetApplication\MarketplaceIntegration;
use JetApplication\Pricelists;
use JetApplication\Product;
use JetApplication\Product_Availability;
use JetApplication\Product_Price;

/**
 *
 */
class Controller_Main extends Admin_EntityManager_WithEShopData_Controller
{
	use Controller_Main_Edit_Images;
	use Controller_Main_Edit_Files;
	use Controller_Main_Edit_Parameters;
	use Controller_Main_Edit_Categories;
	use Controller_Main_Edit_Variants;
	use Controller_Main_Edit_Set;
	use Controller_Main_Edit_Similar;
	use Controller_Main_Edit_Boxes;
	use Controller_Main_Edit_Marketplace;
	use Controller_Main_Edit_Export;
	
	
	
	protected function newItemFactory(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
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
		
		$this->router->addAction('edit_description', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='description';
			} );
		
		
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
		
		$this->router->addAction('edit_similar', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='similar';
			} );
		
		$this->router->addAction('edit_files', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='files';
			} );
		
		$this->router->addAction('edit_boxes', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='boxes';
			} );
		
		$this->router->addAction('marketplace', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='marketplace';
			} );
		
		$this->router->addAction('export', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='export';
			} );
		
	}
	
	protected function getTabs(): array
	{
		$product = $this->current_item;
		
		$_tabs = [
			'main'             => Tr::_('Main data'),
			'description'      => Tr::_('Description'),
			'images'           => Tr::_('Images'),
			'files'            => Tr::_('Files'),
			'parameters'       => Tr::_('Parameters'),
			'categories'       => Tr::_('Categories'),
		];
		
		
		switch($product->getType()) {
			case Product::PRODUCT_TYPE_REGULAR:
				$_tabs['similar'] = Tr::_('Similar products');
				break;
			case Product::PRODUCT_TYPE_VARIANT:
				break;
				
			case Product::PRODUCT_TYPE_VARIANT_MASTER:
				$_tabs['variants'] = Tr::_('Variants');
				$_tabs['similar'] = Tr::_('Similar products');
				break;
			case Product::PRODUCT_TYPE_SET:
				$_tabs['set'] = Tr::_('Product set');
				$_tabs['similar'] = Tr::_('Similar products');
				break;
		}
		
		$_tabs['boxes'] = Tr::_('Boxes');
		
		$marketplaces = MarketplaceIntegration::getActiveModules();
		if($marketplaces) {
			$_tabs['marketplace'] = Tr::_('Marketplace');
		}
		
		$exports = Exports::getExportModulesList();
		if($exports) {
			$_tabs['export'] = Tr::_('Export');
		}

		return $_tabs;
	}
	
	
	public function setupListing() : void
	{
		
		$this->listing_manager->addColumn( new Listing_Column_Name() );
		$this->listing_manager->addColumn( new Listing_Column_ProductKind() );
		$this->listing_manager->addColumn( new Listing_Column_Price() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'active_state',
			'name',
			'kind_of_product',
			'internal_notes',
			'price',
		]);
		
		$this->listing_manager->setSearchWhereCreator(  function( string $search ) : array {
			
			$ft_ids = Admin_Managers::FulltextSearch()->search(
				Product::getEntityType(),
				$search
			);
			
			$code_ids = Product::dataFetchCol(
				select: ['id'],
				where: [
					'internal_code' => $search,
					'OR',
					'ean' => $search
				] );
			
			
			$ids = [];
			if($ft_ids) {
				$ids += array_merge($ids, $ft_ids);
			}
			if($code_ids) {
				$ids = array_merge($ids, $code_ids);
			}
			
			if(!$ids) {
				$ids = [0];
			}
			
			return [
				'id' => $ids,
			];
			
		} );
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Categories() );
		$this->listing_manager->addFilter( new Listing_Filter_ProductKind() );
		$this->listing_manager->addFilter( new Listing_Filter_ProductType() );
		$this->listing_manager->addFilter( new Listing_Filter_Brand() );
		$this->listing_manager->addFilter( new Listing_Filter_Supplier() );
		
		if( MarketplaceIntegration::getActiveModules() ) {
			$this->listing_manager->addFilter( new Listing_Filter_Marketplace() );
		}
		
		
		$this->listing_manager->setCreateBtnRenderer( function() : string {
			return $this->view->render('create_buttons');
		} );
		
	}
	
	protected function getEditEshopDataFieldsRenderer() : ?Closure
	{
		return null;
	}
	
	protected function getAddEshopDataFieldsRenderer() : ?Closure
	{
		return null;
	}
	
	protected function getEditDescriptionFieldsRenderer() : ?Closure
	{
		return null;
	}
	
	protected function getAddDescriptionFieldsRenderer() : ?Closure
	{
		return function( Locale $locale, string $locale_str ) {
			$this->view->setVar('locale', $locale);
			echo $this->view->render('add/description-form-fields');
		};
	}
	
	
	public function edit_main_Action(): void
	{
		$this->handleSetAvailability();
		$this->handleSetPrice();
		
		parent::edit_main_Action();
		
		$this->content->output(
			$this->view->render('edit/main/set-price')
		);
		
	}
	
	public function edit_description_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Description') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		$this->view->setVar('item', $product);
		

		if( $product->catchDescriptionEditForm() ) {
			
			UI_messages::success(
				Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $product->getAdminTitle() ] )
			);
			
			Http_Headers::reload();
		}
		
		$this->output( 'edit/description' );
	
	}
	
	public function handleSetAvailability() : void
	{
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;

		if( ($set_availability_form = $product->getSetAvailabilityForm()) ) {
			$this->view->setVar('set_availability_form', $set_availability_form);
			if($set_availability_form->catch()) {
				foreach(Availabilities::getList() as $availability) {
					Product_Availability::get( $availability, $product->getId() )->save();
				}
				
				Http_Headers::reload();
			}
		}
		
	}
	
	public function handleSetPrice() : void
	{
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		if( ($set_price_form = $product->getSetPriceForm()) ) {
			$this->view->setVar('set_price_form', $set_price_form);
			if($set_price_form->catch()) {
				foreach(Pricelists::getList() as $pricelist) {
					Product_Price::get($pricelist, $product->getId())->save();
				}
				
				Http_Headers::reload();
			}
		}
		
	}
	
}