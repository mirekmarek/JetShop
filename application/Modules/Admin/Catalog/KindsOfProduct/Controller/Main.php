<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Catalog\KindsOfProduct;

use Jet\Application;
use Jet\MVC_Controller_Router;
use Jet\UI;
use JetShop\Application_Admin;
use JetShop\Exports;
use JetShop\Fulltext_Index_Internal_KindOfProduct;
use JetShop\KindOfProduct;

use Jet\MVC_Controller_Default;
use Jet\UI_messages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Logger;
use JetShop\Shops;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	protected ?MVC_Controller_Router $router = null;

	protected ?KindOfProduct $kind_of_product = null;

	/**
	 *
	 * @return MVC_Controller_Router
	 */
	public function getControllerRouter() : MVC_Controller_Router
	{
		if( !$this->router ) {
			$GET = Http_Request::GET();
			
			$kind_id = $GET->getInt( 'id' );
			$action = $GET->getString('action');
			$selected_tab = '';
			
			if($kind_id) {
				$this->kind_of_product = KindOfProduct::get($kind_id);
				
				if($this->kind_of_product) {
					
					$_tabs = [
						'main'    => Tr::_( 'Main data' ),
						'images'  => Tr::_( 'Images' ),
						'detail'  => Tr::_( 'Product detail setting' ),
						'filter'  => Tr::_( 'Listing filter setting' ),
						'hidden_p'=> Tr::_( 'Hidden properties setting' ),
						'exports' => Tr::_( 'Exports' ),
					];
					
					$tabs = UI::tabs(
						$_tabs,
						function($page_id) {
							return Http_Request::currentURI(['page'=>$page_id]);
						},
						Http_Request::GET()->getString('page', 'main')
					);
					
					$selected_tab = $tabs->getSelectedTabId();
					
					$this->view->setVar('tabs', $tabs);
					
				}
			}
			
			
			$this->router = new MVC_Controller_Router( $this );
			$this->router->addAction( 'whisper' )->setResolver(function() use ($GET) {
				return $GET->exists('whisper');
			});
			
			$this->router->setDefaultAction('listing', Main::ACTION_GET_KIND_OF_PRODUCT);
			
			$this->router->getAction('listing')->setResolver(function() use ($action) {
				return (!$this->kind_of_product && !$action) ;
			});
			
			$this->router->addAction('add', Main::ACTION_ADD_KIND_OF_PRODUCT)
				->setResolver(function() use ($action) {
					return ($action=='add' && !$this->kind_of_product);
				})
				->setURICreator(function() {
					return Http_Request::currentURI( ['action' => 'add'], ['id',  'page'] );
				});
			
			$this->router->addAction('delete', Main::ACTION_DELETE_KIND_OF_PRODUCT)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->kind_of_product && $action=='delete';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'action'=>'delete'] );
				});
			
			
			$this->router->addAction('edit_main', Main::ACTION_UPDATE_KIND_OF_PRODUCT)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->kind_of_product && $selected_tab=='main';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id], [ 'page','action'] );
				});
			
			$this->router->addAction('edit_images', Main::ACTION_UPDATE_KIND_OF_PRODUCT)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->kind_of_product && $selected_tab=='images';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'images'], [ 'action'] );
				});
			
			$this->router->addAction('edit_detail_settings', Main::ACTION_UPDATE_KIND_OF_PRODUCT)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->kind_of_product && $selected_tab=='detail';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'detail'], [ 'action'] );
				});
			
			$this->router->addAction('edit_filter_settings', Main::ACTION_UPDATE_KIND_OF_PRODUCT)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->kind_of_product && $selected_tab=='filter';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'filter'], [ 'action'] );
				});
			
			
			$this->router->addAction('edit_hidden_properties_settings', Main::ACTION_UPDATE_KIND_OF_PRODUCT)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->kind_of_product && $selected_tab=='hidden_p';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'hidden_p'], [ 'action'] );
				});
			
			
			$this->router->addAction('edit_exports', Main::ACTION_UPDATE_KIND_OF_PRODUCT)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->kind_of_product && $selected_tab=='exports';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'exports'], [ 'action'] );
				});
			
			
			
			
			
		}

		return $this->router;
	}


	/**
	 *
	 */
	public function listing_Action() : void
	{
		$listing = new Listing();
		$listing->handle();

		$this->view->setVar( 'filter_form', $listing->getFilterForm());
		$this->view->setVar( 'grid', $listing->getGrid() );

		$this->output( 'list' );
	}

	/**
	 *
	 */
	public function add_Action() : void
	{
		Navigation_Breadcrumb::addURL( Tr::_( 'Create a new Kind of product' ) );

		$kind_of_product = new KindOfProduct();


		$form = $kind_of_product->getAddForm();

		if( $kind_of_product->catchAddForm() ) {
			$kind_of_product->save();

			Logger::success(
				event: 'kind_of_product_created',
				event_message: 'Kind of Product created',
				context_object_id: $kind_of_product->getId(),
				context_object_name: $kind_of_product->getInternalName(),
				context_object_data: $kind_of_product
			);


			UI_messages::success(
				Tr::_( 'Kind of Product <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$kind_of_product->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'kind_of_product', $kind_of_product );

		$this->output( 'add' );

	}

	/**
	 *
	 */
	public function edit_main_Action() : void
	{
		$kind_of_product = $this->kind_of_product;

		Navigation_Breadcrumb::addURL( Tr::_( 'Edit kind of product <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] ) );

		$form = $kind_of_product->getEditForm();

		if( $kind_of_product->catchEditForm() ) {

			$kind_of_product->save();

			Logger::success(
				event: 'kind_of_product_updated',
				event_message: 'Kind of Product updated',
				context_object_id: $kind_of_product->getId(),
				context_object_name: $kind_of_product->getInternalName(),
				context_object_data: $kind_of_product
			);

			UI_messages::success(
				Tr::_( 'Kind of Product <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'kind_of_product', $kind_of_product );

		$this->output( 'edit/main' );

	}
	
	public function edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$kind_of_product = $this->kind_of_product;
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit kind of product <b>%ITEM_NAME%</b> - images', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] ) );
		
		foreach(Shops::getList() as $shop) {
			$kind_of_product->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'kind_of_product image',
				object_id: $kind_of_product->getId(),
				object_name: $kind_of_product->getInternalName(),
				upload_event: 'kind_of_product_image_uploaded',
				delete_event: 'kind_of_product_image_deleted'
			);
		}
		
		
		$this->view->setVar( 'kind_of_product', $kind_of_product );
		$this->output( 'edit/images' );
	}
	
	public function edit_detail_settings_Action() : void
	{
		$kind_of_product = $this->kind_of_product;
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit kind of product <b>%ITEM_NAME%</b> - detail settings', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] ) );
		
		$GET = Http_Request::GET();
		
		if($GET->exists('apply_filter_settings')) {
			if($kind_of_product->setDetailByFilter() ) {
				Logger::success(
					event: 'kind_of_product_updated.detail_by_filter',
					event_message: 'Kind of Product updated - detail by filter',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			
			Http_Headers::reload(unset_GET_params: ['apply_filter_settings']);
		}
		
		if(($add_group=$GET->getInt('add_group'))) {
			if($kind_of_product->addDetailPropertyGroup( $add_group )) {
				Logger::success(
					event: 'kind_of_product_updated.detail_group_added',
					event_message: 'Kind of Product updated - detail group '.$add_group.' added',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			Http_Headers::reload(unset_GET_params: ['add_group']);
		}
		
		if(($remove_group=$GET->getInt('remove_group'))) {
			if($kind_of_product->removeDetailPropertyGroup( $remove_group )) {
				Logger::success(
					event: 'kind_of_product_updated.detail_group_removed',
					event_message: 'Kind of Product updated - detail group '.$remove_group.' removed',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			Http_Headers::reload(unset_GET_params: ['remove_group']);
		}
		
		if(($sort_groups=$GET->getString('sort_groups'))) {
			
			$sort_groups = explode('|', $sort_groups);
			if($kind_of_product->sortDetailGroups($sort_groups)) {
				Logger::success(
					event: 'kind_of_product_updated.detail_groups_sorted',
					event_message: 'Kind of Product updated - detail groups sorted',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			
			Http_Headers::reload(unset_GET_params: ['sort_groups']);
		}
		
		if(($add_property=$GET->getInt('add_property'))) {
			$group_id = $GET->getInt('group');
			$group = $kind_of_product->getDetailGroup( $group_id );
			if($group) {
				if($group->addProperty( $add_property )) {
					Logger::success(
						event: 'kind_of_product_updated.detail_property_added',
						event_message: 'Kind of Product updated - detail property '.$add_property.' added',
						context_object_id: $kind_of_product->getId(),
						context_object_name: $kind_of_product->getInternalName(),
						context_object_data: $kind_of_product
					);
					
				}
			}
			Http_Headers::reload(unset_GET_params: ['add_property', 'group']);
		}
		
		if(($property_id=$GET->getInt('set_is_variant_master'))) {
			$state = $GET->getBool('state');
			if($kind_of_product->setPropertyIsVariantMaster( $property_id, $state )) {
				Logger::success(
					event: 'kind_of_product_updated.variant_master_set',
					event_message: 'Kind of Product updated - variant master stet '.$property_id.':'.($state?1:0),
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			Http_Headers::reload(unset_GET_params: ['set_is_variant_master', 'state']);
		}
		
		if(($remove_property=$GET->getInt('remove_property'))) {
			$group_id = $GET->getInt('group');
			$group = $kind_of_product->getDetailGroup( $group_id );
			if($group) {
				if($group->removeProperty( $remove_property )) {
					Logger::success(
						event: 'kind_of_product_updated.detail_property_removed',
						event_message: 'Kind of Product updated - detail property '.$remove_property.' removed',
						context_object_id: $kind_of_product->getId(),
						context_object_name: $kind_of_product->getInternalName(),
						context_object_data: $kind_of_product
					);
					
				}
			}
			Http_Headers::reload(unset_GET_params: ['remove_property', 'group']);
		}
		
		if(($sort_properties=$GET->getString('sort_properties'))) {
			$group_id = $GET->getInt('group');
			$group = $kind_of_product->getDetailGroup( $group_id );
			if($group) {
				$sort_properties = explode('|', $sort_properties);
				if($group->sortProperties( $sort_properties )) {
					Logger::success(
						event: 'kind_of_product_updated.detail_properties_sorted',
						event_message: 'Kind of Product updated - detail group '.$group_id.' properties sorted',
						context_object_id: $kind_of_product->getId(),
						context_object_name: $kind_of_product->getInternalName(),
						context_object_data: $kind_of_product
					);
					
				}
			}
			Http_Headers::reload(unset_GET_params: ['sort_properties', 'group']);
		}
		
		
		$this->view->setVar( 'kind_of_product', $kind_of_product );
		$this->output( 'edit/detail' );
	}
	
	public function edit_filter_settings_Action() : void
	{
		$kind_of_product = $this->kind_of_product;
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit kind of product <b>%ITEM_NAME%</b> - filter settings', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] ) );
		
		$GET = Http_Request::GET();
		
		if($GET->exists('apply_detail_settings')) {
			if($kind_of_product->setFilterByDetail() ) {
				Logger::success(
					event: 'kind_of_product_updated.filter_by_detail',
					event_message: 'Kind of Product updated - filter by detail',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			
			Http_Headers::reload(unset_GET_params: ['apply_detail_settings']);
		}
		
		if(($add_group=$GET->getInt('add_group'))) {
			if($kind_of_product->addFilterPropertyGroup( $add_group )) {
				Logger::success(
					event: 'kind_of_product_updated.filter_group_added',
					event_message: 'Kind of Product updated - filter group '.$add_group.' added',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			Http_Headers::reload(unset_GET_params: ['add_group']);
		}
		
		if(($remove_group=$GET->getInt('remove_group'))) {
			if($kind_of_product->removeFilterPropertyGroup( $remove_group )) {
				Logger::success(
					event: 'kind_of_product_updated.filter_group_removed',
					event_message: 'Kind of Product updated - filter group '.$remove_group.' removed',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			Http_Headers::reload(unset_GET_params: ['remove_group']);
		}
		
		if(($sort_groups=$GET->getString('sort_groups'))) {
			
			$sort_groups = explode('|', $sort_groups);
			if($kind_of_product->sortFilterGroups($sort_groups)) {
				Logger::success(
					event: 'kind_of_product_updated.filter_groups_sorted',
					event_message: 'Kind of Product updated - filter groups sorted',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			
			Http_Headers::reload(unset_GET_params: ['sort_groups']);
		}
		
		if(($add_property=$GET->getInt('add_property'))) {
			$group_id = $GET->getInt('group');
			$group = $kind_of_product->getFilterGroup( $group_id );
			if($group) {
				if($group->addProperty( $add_property )) {
					Logger::success(
						event: 'kind_of_product_updated.filter_property_added',
						event_message: 'Kind of Product updated - filter property '.$add_property.' added',
						context_object_id: $kind_of_product->getId(),
						context_object_name: $kind_of_product->getInternalName(),
						context_object_data: $kind_of_product
					);
					
				}
			}
			Http_Headers::reload(unset_GET_params: ['add_property', 'group']);
		}
		
		if(($remove_property=$GET->getInt('remove_property'))) {
			$group_id = $GET->getInt('group');
			$group = $kind_of_product->getFilterGroup( $group_id );
			if($group) {
				if($group->removeProperty( $remove_property )) {
					Logger::success(
						event: 'kind_of_product_updated.filter_property_removed',
						event_message: 'Kind of Product updated - filter property '.$remove_property.' removed',
						context_object_id: $kind_of_product->getId(),
						context_object_name: $kind_of_product->getInternalName(),
						context_object_data: $kind_of_product
					);
					
				}
			}
			Http_Headers::reload(unset_GET_params: ['remove_property', 'group']);
		}
		
		if(($sort_properties=$GET->getString('sort_properties'))) {
			$group_id = $GET->getInt('group');
			$group = $kind_of_product->getFilterGroup( $group_id );
			if($group) {
				$sort_properties = explode('|', $sort_properties);
				if($group->sortProperties( $sort_properties )) {
					Logger::success(
						event: 'kind_of_product_updated.filter_properties_sorted',
						event_message: 'Kind of Product updated - filter group '.$group_id.' properties sorted',
						context_object_id: $kind_of_product->getId(),
						context_object_name: $kind_of_product->getInternalName(),
						context_object_data: $kind_of_product
					);
					
				}
			}
			Http_Headers::reload(unset_GET_params: ['sort_properties', 'group']);
		}
		
		
		$this->view->setVar( 'kind_of_product', $kind_of_product );
		$this->output( 'edit/filter' );
	}
	
	public function edit_hidden_properties_settings_Action() : void
	{
		
		$kind_of_product = $this->kind_of_product;
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit kind of product <b>%ITEM_NAME%</b> - hidden properties settings', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] ) );
		
		$GET = Http_Request::GET();
		
		if(($add_property=$GET->getInt('add_property'))) {
			if($kind_of_product->addHiddenProperty( $add_property )) {
				Logger::success(
					event: 'kind_of_product_updated.hidden_property_added',
					event_message: 'Kind of Product updated - hidden property '.$add_property.' added',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			Http_Headers::reload(unset_GET_params: ['add_property']);
		}
		
		if(($remove_property=$GET->getInt('remove_property'))) {
			if($kind_of_product->removeHiddenProperty( $remove_property )) {
				Logger::success(
					event: 'kind_of_product_updated.hidden_property_removed',
					event_message: 'Kind of Product updated - hidden property '.$remove_property.' removed',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
			}
			Http_Headers::reload(unset_GET_params: ['remove_property']);
		}

		
		if(($sort_properties=$GET->getString('sort_properties'))) {
			$sort_properties = explode('|', $sort_properties);
			if($kind_of_product->sortHiddenProperties( $sort_properties )) {
				Logger::success(
					event: 'kind_of_product_updated.hidden_properties_sorted',
					event_message: 'Kind of Product updated - hidden properties sorted',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
				
			}

			Http_Headers::reload(unset_GET_params: ['sort_properties']);
		}
		
		
		$this->view->setVar( 'kind_of_product', $kind_of_product );
		$this->output( 'edit/hidden-properties' );
	}
	
	public function edit_exports_Action() : void
	{
		$kind_of_product = $this->kind_of_product;
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit kind of product <b>%ITEM_NAME%</b> - exports', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] ) );
		
		$GET = Http_Request::GET();
		
		$selected_exp = null;
		$selected_exp_shop = null;
		
		$exp = $GET->getString('exp');
		if($exp) {
			$selected_exp = Exports::getActiveModule($exp);
			
			if($selected_exp) {
				$exp_shop = $GET->getString('exp_shop');
				if($exp_shop) {
					$selected_exp_shop = Shops::get($exp_shop);
					if($selected_exp_shop) {
						if(!$selected_exp->isAllowedForShop($selected_exp_shop)) {
							$selected_exp = null;
							$selected_exp_shop = null;
						}
					} else {
						$selected_exp = null;
					}
				}
			}
		}
		
		if($selected_exp_shop) {
			$this->view->setVar('selected_exp', $selected_exp );
			$this->view->setVar('selected_exp_shop', $selected_exp_shop );
			
			$this->view->setVar('selected_exp_code', $selected_exp->getCode());
			$this->view->setVar('selected_exp_shop_key', $selected_exp_shop->getKey() );
			
			$this->view->setVar( 'kind_of_product', $kind_of_product );
		}
		
		
		
		$this->output( 'edit/exports' );
	}
	
	
	public function delete_Action() : void
	{
		$kind_of_product = $this->kind_of_product;

		Navigation_Breadcrumb::addURL(
			Tr::_( 'Delete kind of product  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] )
		);
		
		$this->view->setVar( 'kind_of_product', $kind_of_product );
		
		if($kind_of_product->isItPossibleToDelete( $products, $categories )) {
			if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
				$kind_of_product->delete();
				
				Logger::success(
					event: 'kind_of_product_deleted',
					event_message: 'Kind of Product deleted',
					context_object_id: $kind_of_product->getId(),
					context_object_name: $kind_of_product->getInternalName(),
					context_object_data: $kind_of_product
				);
				
				UI_messages::info(
					Tr::_( 'Kind of Product <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $kind_of_product->getInternalName() ] )
				);
				
				Http_Headers::reload([], ['action', 'id']);
			}
			
			
			
			$this->output( 'delete/confirm' );
			
		} else {
			$this->view->setVar( 'products', $products );
			$this->view->setVar( 'categories', $categories );
			$this->output( 'delete/not-possible' );
			
		}

	}
	
	public function whisper_Action() : void
	{
		$GET = Http_Request::GET();
		
		$result = Fulltext_Index_Internal_KindOfProduct::search(
			search_string: $GET->getString('whisper'),
			only_active: $GET->getBool('only_active')
		);
		
		$this->view->setVar('result', $result);
		echo $this->view->render('search_whisperer_result');
		
		Application::end();
	}
	
}