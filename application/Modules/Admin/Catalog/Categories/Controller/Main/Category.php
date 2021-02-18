<?php
namespace JetShopModule\Admin\Catalog\Categories;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetShop\Category;
use JetShop\Category_ShopData;
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
			$shop_code = $shop->getCode();
			$shop_name = $shop->getName();
			$shop_data = $category->getShopData( $shop_code );

			foreach( Category_ShopData::getImageClasses() as $image_class=>$image_class_name ) {
				$shop_data->catchImageWidget(
					$image_class,
					function() use ($image_class, $category, $shop_code, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'category image '.$image_class.' uploaded', $category->getId().':'.$shop_code, $category->_getPathName().' - '.$shop_name );

					},
					function() use ($image_class, $category, $shop_code, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'category image '.$image_class.' deleted', $category->getId().':'.$shop_code, $category->_getPathName().' - '.$shop_name );
					}
				);

			}
		}

		$this->output( 'category/edit/'.static::getCurrentCategory()->getType().'/images' );
	}

	public function category_edit_exports_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Exports' ) );

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

			$this->logAllowedAction( 'Category created', $new_category->getId(), $new_category->_getPathName() , $new_category );

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
			$this->logAllowedAction( 'Category priority updated', $category->getId(), $category->_getPathName(), $priority );
		}

		Http_Headers::reload([], ['action']);
	}



}