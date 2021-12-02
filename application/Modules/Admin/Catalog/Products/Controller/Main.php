<?php
namespace JetShopModule\Admin\Catalog\Products;


use Jet\AJAX;
use Jet\Logger;
use Jet\MVC_Controller_Router;
use Jet\UI;
use Jet\UI_messages;


use Jet\Application;

use Jet\MVC;
use Jet\MVC_Controller_Default;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use Jet\UI_tabs;
use JetShop\Application_Admin;
use JetShop\Category;
use JetShop\Fulltext_Index_Internal_Product;
use JetShop\Product;

use JetShop\Shops;
use JetShopModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	protected ?MVC_Controller_Router $router = null;

	protected static ?Product $current_product = null;


	public function getControllerRouter() : MVC_Controller_Router
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router( $this );

			$GET = Http_Request::GET();

			$product_id = $GET->getInt('id');
			if($product_id) {
				$product = Product::get( $product_id );

				if($product) {
					static::$current_product = $product;
				}
			}



			$tabs = $this->_getEditTabs();
			if($tabs) {
				$selected_tab = $tabs->getSelectedTabId();

				$this->view->setVar('tabs', $tabs);
			} else {
				$selected_tab = '';
			}

			$action = $GET->getString('action');




			$this->router->setDefaultAction(  'listing', Main::ACTION_GET_PRODUCT  );

			$this->router->addAction( 'whisper' )->setResolver(function() use ($GET) {
				return $GET->exists('whisper');
			});


			$this->router->addAction('add', Main::ACTION_ADD_PRODUCT)
				->setResolver( function() use ($action) {
					return $action=='add';
				} )
				->setURICreator(function() {
					return Http_Request::currentURL(['action'=>'add'], ['id']);
				});

			$this->router->addAction('edit', Main::ACTION_UPDATE_PRODUCT)
				->setResolver( function() use ($action, $selected_tab) {
					return static::$current_product && $selected_tab=='main';
				} )
				->setURICreator(function($id) {
					return Http_Request::currentURL(['id'=>$id], ['action']);
				});

			$this->router->addAction('edit_categories', Main::ACTION_UPDATE_PRODUCT)
				->setResolver( function() use ($action, $selected_tab) {
					return static::$current_product && $selected_tab=='categories';
				} );

			$this->router->addAction('edit_parametrization', Main::ACTION_UPDATE_PRODUCT)
				->setResolver( function() use ($action, $selected_tab) {
					return static::$current_product && $selected_tab=='parametrization';
				} );

			$this->router->addAction('edit_images', Main::ACTION_UPDATE_PRODUCT)
				->setResolver( function() use ($action, $selected_tab) {
					return static::$current_product && $selected_tab=='images';
				} );

			$this->router->addAction('edit_variants', Main::ACTION_UPDATE_PRODUCT)
				->setResolver( function() use ($action, $selected_tab) {
					return static::$current_product && $selected_tab=='variants';
				} );

			$this->router->addAction('edit_set', Main::ACTION_UPDATE_PRODUCT)
				->setResolver( function() use ($action, $selected_tab) {
					return static::$current_product && $selected_tab=='set';
				} );

			$this->router->addAction('edit_exports', Main::ACTION_UPDATE_PRODUCT)
				->setResolver( function() use ($action, $selected_tab) {
					return static::$current_product && $selected_tab=='exports';
				} );

		}

		return $this->router;
	}

	protected function _getEditTabs() : ?UI_tabs
	{
		$product = static::getCurrentProduct();

		if(!$product) {
			return null;
		}

		$_tabs = [
			'main'             => Tr::_('Main data'),
			'categories'       => Tr::_('Categories'),
			'parametrization'  => Tr::_('Parametrization'),
			'images'           => Tr::_('Images'),
			'variants'         => Tr::_('Variants'),
			'set'              => Tr::_('Set'),
			//'exports'          => Tr::_('Exports'),
		];

		switch($product->getType()) {
			case Product::PRODUCT_TYPE_REGULAR:
				break;
			case Product::PRODUCT_TYPE_VARIANT_MASTER:
				unset($_tabs['set']);
				break;
			case Product::PRODUCT_TYPE_VARIANT:
				unset($_tabs['variants']);
				unset($_tabs['set']);
				break;
			case Product::PRODUCT_TYPE_SET:
				unset($_tabs['variants']);
				break;
		}

		return UI::tabs(
			$_tabs,
			function($page_id) use ($product) {
				return '?id='.$product->getId().'&page='.$page_id;
			},
			Http_Request::GET()->getString('page')
		);

	}

	public static function getCurrentProduct() : ?Product
	{
		return self::$current_product;
	}

	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		UI_module::initBreadcrumb();

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		} else {
			$product = static::getCurrentProduct();

			if($product) {
				Navigation_Breadcrumb::reset();

				$page = MVC::getPage();
				Navigation_Breadcrumb::addURL(
					UI::icon( $page->getIcon() ).'&nbsp;&nbsp;'.$page->getBreadcrumbTitle(),
					Http_Request::currentURI([], ['id'])
				);


				Navigation_Breadcrumb::addURL( $product->getAdminTitle() );
			}
		}
	}

	public function listing_Action() : void
	{

		$this->_setBreadcrumbNavigation();

		$listing = new Listing();
		$listing->handle();

		$this->view->setVar( 'filter_form', $listing->filter_getForm());
		$this->view->setVar( 'grid', $listing->getGrid() );
		$this->view->setVar( 'listing', $listing );

		$this->output( 'list' );


	}

	public function edit_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$product = static::getCurrentProduct();

		if( $product->catchEditForm() ) {

			$product->save();
			$product->syncVariants();
			Category::syncCategories();

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

		$this->output( 'edit/main' );
	}



	public function edit_images_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$GET = Http_Request::GET();

		if($GET->exists('action')) {
			$product = static::getCurrentProduct();
			$shop = Shops::get( $GET->getString('shop_key') );

			$shop_data = $product->getShopData($shop);
			$this->view->setVar('shop', $shop );

			$updated = false;
			switch($GET->getString('action')) {
				case 'upload':
					Application_Admin::handleUploadTooLarge();

					$shop_data->uploadImages();
					$updated = true;
					break;
				case 'delete':
					$shop_data->deleteImages( explode(',', $GET->getString('images')) );
					$updated = true;
					break;
				case 'save_sort':
					$shop_data->sortImages( explode(',', $GET->getString('images')) );
					$updated = true;
					break;
			}

			if($updated) {
				$product->save();

				AJAX::response(
					[
						'result' => 'ok',
						'snippets' => [
							'images_'.$shop->getKey() => $this->view->render('edit/images/list')
						]

					]
				);

			}
		}



		$this->output( 'edit/images' );

	}


	public function edit_variants_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$product = static::getCurrentProduct();

		$updated = false;
		$sync = false;

		if( $product->catchVariantSetupForm() ) {
			$updated = true;
			$sync = true;
		}


		$new_variant = new Product();

		if( $product->catchAddVariantForm( $new_variant ) ) {
			$updated = true;
		}

		if( $product->catchUpdateVariantsForm() ) {
			$updated = true;
		}


		if($updated) {
			$product->save();
			if($sync) {
				$product->syncVariants();
			}
			Category::syncCategories();

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


		$this->view->setVar('new_variant', $new_variant);

		//TODO: it's shit ... revision needed

		$this->output( 'edit/variants' );
	}

	public function edit_set_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$product = static::getCurrentProduct();

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

			Category::syncCategories();

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

		$this->output( 'edit/set' );
	}



	public function edit_categories_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$product = static::getCurrentProduct();

		$allowed = true;
		if(
			$product->getType()==Product::PRODUCT_TYPE_VARIANT &&
			($master=Product::get($product->getVariantMasterProductId())) &&
			$master->isVariantSyncCategories()
		) {
			$allowed = false;
		}

		if($allowed) {
			$POST = Http_Request::POST();

			$updated = false;
			switch($POST->getString('action')) {
				case 'add_category':
					if($product->addCategory( $POST->getInt('category_id') )) {
						$updated = true;
					}
					break;
				case 'remove_category':
					if($product->removeCategory( $POST->getInt('category_id') )) {
						$updated = true;
					}
					break;
				case 'set_main_category':
					if($product->setMainCategory( $POST->getInt('category_id') )) {
						$updated = true;
					}
					break;
			}

			if($updated) {
				$product->save();
				$product->syncVariants();
				Category::syncCategories();

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
		} else {
			$product->getEditForm()->setIsReadonly();
		}

		$this->output( 'edit/categories' );
	}

	public function edit_parametrization_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$product = static::getCurrentProduct();

		if( $product->catchParametrizationEditForm() ) {

			$product->save();
			$product->syncVariants();
			Category::syncCategories();

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

		$this->output( 'edit/parametrization' );
	}


	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new product' ) );

		$product = new Product();


		$form = $product->getAddForm();

		if( $product->catchAddForm() ) {
			$product->save();
			Category::syncCategories();

			Logger::success(
				'product_created',
				'Product '.$product->getAdminTitle().' ('.$product->getId().') created',
				$product->getId(),
				$product->getAdminTitle(),
				$product
			);

			UI_messages::success(
				Tr::_( 'Product <b>%NAME%</b> has been created', [ 'NAME' => $product->getAdminTitle() ] )
			);

			Http_Headers::movedTemporary( $product->getEditURL() );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'product', $product );

		$this->output( 'add' );

	}


	public function delete_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Delete product' ) );

		$product = static::$current_product;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete product <b>%NAME%</b>', [ 'NAME' => $product->getAdminTitle() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$product->delete();
			Logger::success(
				'product_deleted',
				'Product '.$product->getAdminTitle().' ('.$product->getId().') deleted',
				$product->getId(),
				$product->getAdminTitle(),
				$product
			);

			UI_messages::info(
				Tr::_( 'Product <b>%NAME%</b> has been deleted', [ 'NAME' => $product->getAdminTitle() ] )
			);

			Http_Headers::movedTemporary( MVC::getPage()->getURLPath() );
		}


		$this->view->setVar( 'product', $product );

		$this->output( 'delete-confirm' );
	}

	public function whisper_Action() : void
	{
		$GET = Http_Request::GET();


		$result = Fulltext_Index_Internal_Product::search(
			$GET->getString('whisper'),
			$GET->getBool('only_active'),
			json_decode($GET->getRaw('filter'))
		);

		$this->view->setVar('result', $result);
		echo $this->view->render('search_whisperer_result');

		Application::end();
	}

}