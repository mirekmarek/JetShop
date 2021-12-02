<?php
namespace JetShopModule\Admin\Catalog\Categories;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\Tr;
use Jet\UI_messages;
use JetShop\Category;

use JetShop\Exports;
use JetShop\Shops;
use JetShop\Application_Admin;

trait Controller_Main_Category
{

	public function getControllerRouter_category( string $action, string $selected_tab ) : void
	{
		if(static::$current_param_group) {
			return;
		}

		$this->router->addAction( 'category_add_regular', Main::ACTION_ADD_CATEGORY )
			->setResolver(function() use ($action) {
				return ( $action=='create_regular' );
			});

		$this->router->addAction( 'category_add_virtual', Main::ACTION_ADD_CATEGORY )->setResolver(function() use ($action) {
			return ( $action=='create_virtual' );
		});

		$this->router->addAction( 'category_add_link', Main::ACTION_ADD_CATEGORY )->setResolver(function() use ($action) {
			return ( $action=='create_link' );
		});

		$this->router->addAction( 'category_add_top', Main::ACTION_ADD_CATEGORY )->setResolver(function() use ($action) {
			return ( $action=='create_top' );
		});

		if(!static::$current_category) {
			return;
		}


		$this->router->addAction( 'save_sort', Main::ACTION_GET_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $action=='save_sort';
		});

		if($action!='') {
			return;
		}

		$this->router->addAction( 'category_edit', Main::ACTION_GET_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='main';
		});

		$this->router->addAction( 'category_edit_filter', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='filter';
		});

		$this->router->addAction( 'category_edit_images', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='images';
		});

		$this->router->addAction( 'category_edit_exports', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='exports';
		});

		$this->router->addAction( 'category_edit_param_strategy', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='parametrization';
		});


	}


	public function category_edit_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit' ) );
		$category = static::getCurrentCategory();

		if( $category->catchEditForm() ) {
			$this->_editCategorySave();
		}

		$this->view->setVar('toolbar', 'category/edit/'.static::getCurrentCategory()->getType().'/main/toolbar');



		if( static::getCurrentCategory()->getCanDefineProperties() ) {
			$sort_items = [];

			foreach($category->getParametrizationGroups() as $group) {
				if(!$group->isInherited()) {
					$sort_items[$group->getId()] = $group->getShopData()->getLabel();
				}
			}

			$can_sort_groups = count($sort_items)>1;

			$this->view->setVar('can_sort_groups', $can_sort_groups);

			if($can_sort_groups) {
				$this->view->setVar('sort_items', $sort_items);
				$this->view->setVar('sort_title', Tr::_('Sort groups'));
				$this->view->setVar('sort_action', 'save_sort_groups');
			}
		}

		$this->output( 'category/edit/'.static::getCurrentCategory()->getType().'/main' );
	}

	public function category_edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();

		$this->_setBreadcrumbNavigation( Tr::_( 'Images' ) );

		$category = static::getCurrentCategory();

		foreach(Shops::getList() as $shop) {
			$category->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'Category image',
				object_id: $category->getId(),
				object_name: $category->_getPathName(),
				upload_event: 'category_image_uploaded',
				delete_event: 'category_image_deleted'
			);
		}

		$this->output( 'category/edit/'.static::getCurrentCategory()->getType().'/images' );
	}

	public function category_edit_exports_Action()
	{

		$this->_setBreadcrumbNavigation( Tr::_( 'Exports' ) );

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

			$this->view->setVar('category', static::$current_category);
		}



		$this->output( 'category/edit/'.static::getCurrentCategory()->getType().'/exports' );
	}


	public function category_edit_filter_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit - filter settings' ) );
		$category = static::getCurrentCategory();

		if( $category->catchTargetFilterEditForm() ) {
			$this->_editCategorySave();
		}

		$this->output( 'category/edit/'.static::getCurrentCategory()->getType().'/filter' );

	}
	public function category_edit_param_strategy_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit - parametrization strategy' ) );

		$category = static::getCurrentCategory();

		if( $category->catchParametrizationStrategyForm() ) {
			$this->_editCategorySave();
		}

		$this->view->setVar('toolbar', 'category/edit/'.static::getCurrentCategory()->getType().'/parametrization_strategy/toolbar');

		$this->output( 'category/edit/'.static::getCurrentCategory()->getType().'/parametrization_strategy' );


	}

	public function category_add_regular_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'New regular category' ) );
		$this->_category_add( Category::CATEGORY_TYPE_REGULAR );
	}

	public function category_add_virtual_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'New virtual category' ) );
		$this->_category_add( Category::CATEGORY_TYPE_VIRTUAL );
	}

	public function category_add_top_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'New top category' ) );
		$this->_category_add( Category::CATEGORY_TYPE_TOP );
	}

	public function category_add_link_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'New link' ) );
		$this->_category_add( Category::CATEGORY_TYPE_LINK );
	}



	protected function _category_add( string $type )
	{
		$new_category = new Category();
		$new_category->setType( $type );
		$new_category->setParentId( static::getCurrentCategoryId() );

		$this->_setBreadcrumbNavigation( Tr::_( '<b>Create new</b>&nbsp;%TYPE%', [ 'TYPE' => $new_category->getTypeTitle() ] ) );


		if($new_category->catchAddForm()) {
			$new_category->save();

			Logger::success(
				event: 'category_created',
				event_message: 'Category '.$new_category->_getPathName().' ('.$new_category->getId().') created',
				context_object_id: $new_category->getId(),
				context_object_name: $new_category->_getPathName(),
				context_object_data: $new_category
			);

			UI_messages::success(
				Tr::_( 'Category <b>%NAME%</b> has been created', [ 'NAME' => $new_category->_getPathName() ] )
			);

			Http_Headers::movedTemporary( $new_category->getEditURL() );
		}

		$this->view->setVar( 'new_category', $new_category );

		$this->view->setVar('tabs', '');
		$this->view->setVar('toolbar', 'category/add/'.$type.'/toolbar');

		$this->output( 'category/add/'.$type );

	}

	public function save_sort_Action() : void
	{
		$POST = Http_Request::POST();

		$categories = explode('|', $POST->getString('categories_sort_order'));

		$priority = 0;
		foreach( $categories as $id ) {
			$category = Category::get( (int)$id );
			if(!$category) {
				continue;
			}
			$priority++;

			$category->setPriority( $priority );
			$category->save();
			Logger::success(
				event: 'category_priority_updated',
				event_message: 'Category '.$category->_getPathName().' ('.$category->getId().') priority updated',
				context_object_id: $category->getId(),
				context_object_name: $category->_getPathName(),
				context_object_data: $priority
			);
		}

		Http_Headers::reload([], ['action']);
	}



}