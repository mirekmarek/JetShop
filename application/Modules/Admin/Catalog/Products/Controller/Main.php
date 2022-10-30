<?php
namespace JetShopModule\Admin\Catalog\Products;


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
use JetShop\Category;
use JetShop\Fulltext_Index_Internal_Product;
use JetShop\Product;

use JetShopModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	use Controller_Main_Edit_Main;
	use Controller_Main_Edit_Images;
	use Controller_Main_Edit_Parameters;
	use Controller_Main_Edit_Categories;
	use Controller_Main_Edit_Variants;
	use Controller_Main_Edit_Set;

	use Controller_Main_Listing_Export;
	
	protected ?MVC_Controller_Router $router = null;

	protected static ?Product $current_product = null;
	
	protected ?Listing $listing = null;


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
			
			$this->router->addAction( 'listing_operation' )->setResolver(function() use ($GET) {
				return $GET->exists('listing_operation');
			});
			
			$this->router->addAction( 'whisper' )->setResolver(function() use ($GET) {
				return $GET->exists('whisper');
			});
			
			$this->router->addAction( 'export' )->setResolver(function() use ($GET) {
				return $GET->exists('export');
			});
			

			$this->router->addAction('add', Main::ACTION_ADD_PRODUCT)
				->setResolver( function() use ($action) {
					return $action=='add';
				} )
				->setURICreator(function() {
					return Http_Request::currentURL(['action'=>'add'], ['id']);
				});

			$this->router->addAction('edit_main', Main::ACTION_UPDATE_PRODUCT)
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

			$this->router->addAction('edit_parameters', Main::ACTION_UPDATE_PRODUCT)
				->setResolver( function() use ($action, $selected_tab) {
					return static::$current_product && $selected_tab=='parameters';
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
			'parameters'       => Tr::_('Parameters'),
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
	
	public function getListing() : Listing
	{
		if(!$this->listing) {
			$this->listing = new Listing();
			$this->listing->handle();
		}
		
		return $this->listing;
	}

	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		$page = MVC::getPage();
		
		Navigation_Breadcrumb::reset();
		
		Navigation_Breadcrumb::addURL(
			UI::icon( $page->getIcon() ).'&nbsp;&nbsp;'.$page->getBreadcrumbTitle(),
			Http_Request::currentURI(unset_GET_params: [
				'id',
				'listing_operation',
				'p',
				'listing_operation_confirm'
			])
		);

		
		$listing = $this->getListing();
		$c_filter = $listing->getFilter(Listing_Filter::CATEGORIES);

		
		if($c_filter->getMode()==Listing_Filter_Categories::MODE_TREE ) {

			$tree = Category::getTree();
			$filter = $this->getListing()->getFilter(Listing_Filter::CATEGORIES);
			
			$current_cat_id = $filter->getCurrentCategoryId();
			
			if(
				( $current_node = $tree->getNode( $filter->getCurrentCategoryId() ) )
			) {

				foreach( $current_node->getPath() as $node ) {
					if(!$node->getId()) {
						continue;
					}
					
					$label = $node->getLabel();
					
					if(!$label) {
						$label = '???';
					}
					
					Navigation_Breadcrumb::addURL( $label, $filter->getCategoryUrl( $node->getId() ) );
					
				}
			
			}
		}

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		} else {
			$product = static::getCurrentProduct();

			if($product) {
				Navigation_Breadcrumb::addURL( $product->getAdminTitle() );
			}
		}
	}

	public function listing_Action() : void
	{
		$this->_setBreadcrumbNavigation();
		
		$schema = Listing_Schema::getSelectedSchemaDefinition();
		if($schema->catchUpdateSchemaForm()) {
			Http_Headers::movedTemporary( $schema->getURL() );
		}
		
		if( $new_schema=Listing_Schema::catchAddSchemaForm() ) {
			Http_Headers::movedTemporary( $new_schema->getURL() );
		}

		$listing = $this->getListing();

		$this->view->setVar( 'filter_form', $listing->getFilterForm());
		$this->view->setVar( 'grid', $listing->getGrid() );
		$this->view->setVar( 'listing', $listing );

		$this->output( 'list' );


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

	public function listing_operation_Action() : void
	{
		$GET = Http_Request::GET();
		
		$returnRedirect = function() {
			Http_Headers::reload(unset_GET_params: [
				'listing_operation',
				'p',
				'listing_operation_confirm'
			]);
		};
		
		$listing = $this->getListing();
		$operation = $GET->getString('listing_operation');
		if(!$listing->operationExists($operation)) {
			$returnRedirect();
		}
		
		$products = [];
		$p_ids = $GET->getRaw('p');
		
		foreach($p_ids as $p_id) {
			$p_id = (int)$p_id;
			if($p_id>0) {
				$product = Product::get( $p_id );
				if($product) {
					$products[$p_id] = $product;
				}
			}
		}
		
		if(!$products) {
			$returnRedirect();
		}
		
		$operation = $listing->operation($operation);
		
		$this->_setBreadcrumbNavigation(
			Tr::_($operation->getTitle())
		);
		
		if(
			$GET->exists('listing_operation_confirm') &&
			$operation->isPrepared()
		) {
			$operation->perform( $products );
			$returnRedirect();
		}
		
		
		$this->view->setVar('listing', $listing);
		$this->view->setVar('operation', $operation);
		$this->view->setVar('products', $products);
		$this->output( 'listing/operation' );

	}
}