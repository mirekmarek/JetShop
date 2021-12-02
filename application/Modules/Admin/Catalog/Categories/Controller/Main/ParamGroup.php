<?php
namespace JetShopModule\Admin\Catalog\Categories;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\Tr;
use JetShop\Application_Admin;
use JetShop\Parametrization_Group;
use JetShop\Parametrization_Group_ShopData;
use JetShop\Shops;

trait Controller_Main_ParamGroup
{
	public function getControllerRouter_group( string $action, string $selected_tab ) : void
	{
		if(!static::$current_category) {
			return;
		}

		if(!static::$current_param_group) {
			$this->router->addAction( 'save_sort_groups', Main::ACTION_GET_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
				return $action=='save_sort_groups';
			});

			$this->router->addAction( 'group_add', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
				return $action=='create_group';
			});

			return;
		}

		if( static::$current_param_property || $action!='' ) {
			return;
		}

		$this->router->addAction( 'group_edit', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='main';
		});

		$this->router->addAction( 'group_edit_images', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='images';
		});

	}


	public function group_edit_Action() : void
	{
		$group = static::getCurrentParamGroup();

		$this->_setBreadcrumbNavigation( Tr::_('Main settings') );

		$can_edit_parametrization = !$group->isInherited();

		if($can_edit_parametrization) {
			if($group->catchEditForm()) {
				$this->_editCategorySave();
			}

			$this->view->setVar('toolbar', 'param/group/edit/main/toolbar');


			$sort_items = [];
			foreach($group->getProperties() as $property) {
				if(!$property->isInherited()) {
					$sort_items[$property->getId()] = $property->getShopData()->getLabel();
				}
			}
			$can_sort_properties = count($sort_items)>1;

			$this->view->setVar('can_sort_properties', $can_sort_properties);


			if($can_sort_properties) {
				$this->view->setVar('sort_items', $sort_items);
				$this->view->setVar('sort_title', Tr::_('Sort properties'));
				$this->view->setVar('sort_action', 'save_sort_properties');
			}



		} else {
			$group->getEditForm()->setIsReadonly( true );
		}

		$this->output( 'param/group/edit/main' );
	}



	public function group_edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();

		$group = static::getCurrentParamGroup();

		$this->_setBreadcrumbNavigation( Tr::_('Images') );

		if(!$group->isInherited()) {
			foreach(Shops::getList() as $shop) {
				$group->getShopData( $shop )->catchImageWidget(
					shop: $shop,
					entity_name: 'Param.group image',
					object_id: $group->getId(),
					object_name: $group->getFilterLabel(),
					upload_event: 'param.group_image_uploaded',
					delete_event: 'param.group_image_deleted'
				);
			}
		} else {
			foreach(Shops::getList() as $shop) {
				$shop_data = $group->getShopData( $shop );

				foreach( Parametrization_Group_ShopData::getImageClasses() as $image_class=>$image_class_name ) {
					$shop_data->getImageUploadForm( $image_class )->setIsReadonly();
					$shop_data->getImageDeleteForm( $image_class )->setIsReadonly();
				}
			}
		}


		$this->output( 'param/group/edit/images' );
	}


	public function group_add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_('New group') );

		$category = static::getCurrentCategory();
		if($category->getCanDefineProperties()) {
			$new_group = new Parametrization_Group();

			if($new_group->catchAddForm()) {
				$category->addParametrizationGroup( $new_group );

				$this->_editCategorySave(false);

				Http_Headers::reload(['group_id'=>$new_group->getId()], ['action']);
			}

			$this->view->setVar('new_group', $new_group);
		}

		Controller_Main::$current_action = 'group_add';

		$this->view->setVar('tabs', '');
		$this->view->setVar('toolbar', 'param/group/add/toolbar');
		$this->output( 'param/group/add' );
	}



	public function save_sort_groups_Action() : void
	{
		$category = static::getCurrentCategory();

		$ids = explode('|', Http_Request::POST()->getString('sort_order'));
		$priority = 0;
		foreach( $ids as $id ) {
			$id = (int)$id;

			$group = $category->getParametrizationGroup($id);
			if(!$group) {
				continue;
			}
			$priority++;

			$group->setPriority( $priority );
			$group->save();

			Logger::success(
				'param.group_priority_updated',
				'Param. group '.$group->getShopData()->getLabel().' ('.$group->getId().') priority updated',
				$group->getId(),
				$group->getShopData()->getLabel(),
				$priority
			);
		}

		Http_Headers::reload([], ['action']);
	}

}