<?php
namespace JetShopModule\Admin\Catalog\Categories;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\Tr;
use JetShop\Application_Admin;
use JetShop\Parametrization_Property;
use JetShop\Parametrization_Property_ShopData;
use JetShop\Shops;

trait Controller_Main_ParamProperty
{
	public function getControllerRouter_property( string $action, string $selected_tab ) : void
	{
		if(
			!static::$current_param_group ||
			static::$current_param_property_option
		) {
			return;
		}

		if(!static::$current_param_property) {
			$this->router->addAction( 'save_sort_properties', Main::ACTION_GET_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
				return $action=='save_sort_properties';
			});

			$this->router->addAction( 'property_add', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
				return $action=='create_property';
			});

			return;
		}


		if($action!='') {
			return;
		}

		$this->router->addAction( 'property_edit', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='main';
		});

		$this->router->addAction( 'property_edit_images', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='images';
		});

	}

	public function property_edit_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_('Main settings') );

		$group = static::getCurrentParamGroup();
		$property = static::getCurrentParamProperty();


		if(!$group->isInherited()) {
			if($property->catchEditForm()) {
				$this->_editCategorySave();
			}

			$this->view->setVar('toolbar', 'param/property/edit/main/toolbar/'.$property->getType());

			if($property->getType()==Parametrization_Property::PROPERTY_TYPE_OPTIONS) {
				$sort_items = [];
				if($property->getType()==Parametrization_Property::PROPERTY_TYPE_OPTIONS) {
					foreach($property->getOptions() as $option) {
						$sort_items[$option->getId()] = $option->getShopData()->getFilterLabel();
					}
				}

				$can_sort_options = count($sort_items)>1;

				$this->view->setVar('can_sort_options', $can_sort_options);

				if($can_sort_options) {
					$this->view->setVar('sort_items', $sort_items);
					$this->view->setVar('sort_title', Tr::_('Sort options'));
					$this->view->setVar('sort_action', 'save_sort_options');
				}
			}


		} else {
			$property->getEditForm()->setIsReadonly(true);
		}


		$this->view->setVar('property', $property);

		$this->output( 'param/property/edit/main' );
	}

	public function property_edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();

		$this->_setBreadcrumbNavigation( Tr::_('Images') );

		$group = static::getCurrentParamGroup();
		$property = static::getCurrentParamProperty();


		if(!$group->isInherited()) {
			foreach(Shops::getList() as $shop) {
				$property->getShopData( $shop )->catchImageWidget(
					shop: $shop,
					entity_name: 'Param.property image',
					object_id: $property->getId(),
					object_name: $property->getLabel(),
					upload_event: 'param.property_image_uploaded',
					delete_event: 'param.property_image_deleted'
				);
			}
		} else {
			foreach(Shops::getList() as $shop) {
				$shop_data = $property->getShopData( $shop );

				foreach( Parametrization_Property_ShopData::getImageClasses() as $image_class=>$image_class_name ) {
					$shop_data->getImageUploadForm( $image_class )->setIsReadonly();
					$shop_data->getImageDeleteForm( $image_class )->setIsReadonly();
				}
			}

		}

		$this->output( 'param/property/edit/images' );
	}


	public function property_add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_('New property') );

		$category = static::getCurrentCategory();
		$GET = Http_Request::GET();

		$group = $category->getParametrizationGroup( $GET->getInt('group_id') );
		$type = $GET->getString('type', '', array_keys(Parametrization_Property::getTypes()));

		if( $group && $type && !$group->isInherited() ) {
			static::$current_param_group = $group;

			$class_name = "JetShop\Parametrization_Property_".$type;

			/**
			 * @var Parametrization_Property $new_property
			 */
			$new_property = new $class_name();


			if($new_property->catchAddForm()) {

				$category->addParametrizationProperty( $group->getId(), $new_property );

				$this->_editCategorySave(false);

				Http_Headers::reload(['property_id'=>$new_property->getId()], ['action', 'type']);
			}
			$this->view->setVar('new_property', $new_property);
		}

		Controller_Main::$current_action = 'property_add';

		$this->view->setVar('tabs', '');
		$this->view->setVar('toolbar', 'param/property/add/toolbar');
		$this->output( 'param/property/add' );
	}



	public function save_sort_properties_Action() : void
	{
		$category = static::getCurrentCategory();
		$GET = Http_Request::GET();

		$group = $category->getParametrizationGroup( $GET->getInt('group_id') );
		if($group) {

			$ids = explode('|', Http_Request::POST()->getString('sort_order'));
			$priority = 0;
			foreach( $ids as $id ) {
				$property = $group->getProperty($id);
				if(!$property) {
					continue;
				}
				$priority++;

				$property->setPriority( $priority );
				$property->save();

				Logger::success(
					'param.property_priority_updated',
					'Param. property '.$group->getShopData()->getLabel().' ('.$group->getId().') priority updated',
					$group->getId(),
					$group->getShopData()->getLabel(),
					$priority
				);
			}
		}


		Http_Headers::reload([], ['action']);
	}

}