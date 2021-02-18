<?php
namespace JetShopModule\Admin\Catalog\Categories;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetShop\Application_Admin;
use JetShop\Parametrization_Property_Option;
use JetShop\Parametrization_Property_Option_ShopData;
use JetShop\Shops;
use JetShop\Parametrization_Property;

trait Controller_Main_ParamOption
{
	public function getControllerRouter_option( string $action, string $selected_tab ) : void
	{
		if(!static::getCurrentParamProperty()) {
			return;
		}

		if( !static::$current_param_property_option ) {
			$this->router->addAction( 'save_sort_options', Main::ACTION_GET_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
				return $action=='save_sort_options';
			});

			if(static::getCurrentParamProperty()->getType()==Parametrization_Property::PROPERTY_TYPE_OPTIONS) {
				$this->router->addAction( 'option_add', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
					return $action=='create_option';
				});
			}

			return;
		}

		if($action!='') {
			return;
		}

		$this->router->addAction( 'option_edit', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='main';
		});

		$this->router->addAction( 'option_edit_images', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
			return $selected_tab=='images';
		});
	}


	public function option_edit_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_('Main settings') );

		$option = static::getCurrentParamPropertyOption();


		if(!$option->isInherited()) {
			if($option->catchEditForm()) {
				$this->_editCategorySave();
			}

			$this->view->setVar('toolbar', 'param/option/edit/main/toolbar');

		} else {
			$option->getEditForm()->setIsReadonly(true);
		}

		$this->view->setVar('option', $option);


		$this->output( 'param/option/edit/main' );

	}

	public function option_edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();

		$this->_setBreadcrumbNavigation( Tr::_('Images') );

		$option = static::getCurrentParamPropertyOption();

		if(!$option->isInherited()) {
			foreach(Shops::getList() as $shop) {
				$shop_code = $shop->getCode();
				$shop_name = $shop->getName();
				$shop_data = $option->getShopData( $shop_code );

				foreach( Parametrization_Property_Option_ShopData::getImageClasses() as $image_class=>$image_class_name ) {
					$shop_data->catchImageWidget(
						$image_class,
						function() use ($image_class, $option, $shop_code, $shop_name, $shop_data) {
							$shop_data->save();

							$this->logAllowedAction( 'prop.property image '.$image_class.' uploaded', $option->getId().':'.$shop_code, $shop_data->getFilterLabel().' - '.$shop_name );

						},
						function() use ($image_class, $option, $shop_code, $shop_name, $shop_data) {
							$shop_data->save();

							$this->logAllowedAction( 'prop.property image '.$image_class.' deleted', $option->getId().':'.$shop_code, $shop_data->getFilterLabel().' - '.$shop_name );
						}
					);

				}
			}
		} else {
			foreach(Shops::getList() as $shop) {
				$shop_data = $option->getShopData( $shop->getCode() );

				foreach( Parametrization_Property_Option_ShopData::getImageClasses() as $image_class=>$image_class_name ) {
					$shop_data->getImageUploadForm( $image_class )->setIsReadonly();
					$shop_data->getImageDeleteForm( $image_class )->setIsReadonly();
				}
			}
		}


		$this->output( 'param/option/edit/images' );
	}







	public function option_add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_('New option') );

		$category = static::getCurrentCategory();
		$GET = Http_Request::GET();

		$group = $category->getParametrizationGroup( $GET->getInt('group_id') );
		$property = $category->getParametrizationProperty( $GET->getInt('property_id') );

		if( $group && $property && !$group->isInherited() ) {
			static::$current_param_group = $group;
			static::$current_param_property = $property;

			$new_option = new Parametrization_Property_Option();

			if($new_option->catchAddForm()) {
				$property->addOption( $new_option );

				$this->_editCategorySave();
			}

			$this->view->setVar('new_option', $new_option);

		}

		Controller_Main::$current_action = 'option_add';

		$this->view->setVar('tabs', '');
		$this->view->setVar('toolbar', 'param/option/add/toolbar');
		$this->output( 'param/option/add' );
	}

	public function save_sort_options_Action() : void
	{
		$category = static::getCurrentCategory();
		$GET = Http_Request::GET();

		$group = $category->getParametrizationGroup( $GET->getInt('group_id') );
		$property = $category->getParametrizationProperty( $GET->getInt('property_id') );

		if($group && $property) {

			$ids = explode('|', Http_Request::POST()->getString('sort_order'));
			$priority = 0;
			foreach( $ids as $id ) {
				$option = $property->getOption($id);
				if(!$option) {
					continue;
				}
				$priority++;

				$option->setPriority( $priority );
				$option->save();
				$this->logAllowedAction( 'prop.property.option priority updated', $option->getId(), $option->getShopData()->getFilterLabel(), $priority );
			}
		}

		Http_Headers::reload([], ['action']);
	}


}