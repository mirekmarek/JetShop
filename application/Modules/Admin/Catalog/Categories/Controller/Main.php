<?php
namespace JetShopModule\Admin\Catalog\Categories;


use Jet\AJAX;
use Jet\Logger;
use Jet\MVC_Controller_Router;
use JetShop\Category;
use JetShop\Parametrization_Group;
use JetShop\Parametrization_Property;
use JetShop\Parametrization_Property_Option;
use JetShop\Fulltext_Index_Internal_Category;

use Jet\MVC_Controller_Default;

use Jet\UI;
use Jet\UI_messages;
use Jet\UI_tabs;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Data_Tree_Node;

use JetShop\Shops;
use Jet\Application;
use JetShopModule\Admin\UI\Main as UI_module;

use JetShop\Stencil_Option;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	use Controller_Main_Category;
	use Controller_Main_ParamGroup;
	use Controller_Main_ParamProperty;
	use Controller_Main_ParamOption;
	
	protected ?MVC_Controller_Router $router = null;

	protected static ?Category $current_category = null;

	protected static ?Parametrization_Group $current_param_group = null;

	protected static ?Parametrization_Property $current_param_property = null;

	protected static Parametrization_Property_Option|Stencil_Option|null $current_param_property_option = null;

	protected static string $current_action = '';


	public function getControllerRouter() : MVC_Controller_Router
	{

		if( !$this->router ) {
			$this->router = new MVC_Controller_Router( $this );

			$GET = Http_Request::GET();

			$category_id = $GET->getInt('id');
			if($category_id) {
				$category = Category::get($category_id);

				if($category) {
					static::$current_category = $category;

					$group_id = $GET->getInt('group_id');
					if($group_id) {
						$group = $category->getParametrizationGroup( $group_id );

						if($group) {
							static::$current_param_group = $group;

							$property_id = $GET->getInt('property_id');

							if($property_id) {
								$property = $category->getParametrizationProperty( $property_id );

								if($property) {
									static::$current_param_property = $property;

									$option_id = $GET->getInt('option_id');
									if($option_id) {
										$option = $property->getOption( $option_id );

										if($option) {
											static::$current_param_property_option = $option;
										}
									}
								}
							}
						}
					}
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

			$this->router->setDefaultAction(  'default', Main::ACTION_GET_CATEGORY  );

			$this->router->addAction( 'whisper' )->setResolver(function() use ($GET) {
				return $GET->exists('whisper');
			});

			$this->router->addAction( 'set_filter' )->setResolver(function() use ($GET) {
				return $GET->exists('set_filter');
			});

			$this->router->addAction( 'generate_url_path_part' )->setResolver(function() use ($GET) {
				return $GET->exists('generate_url_path_part');
			});

			$this->getControllerRouter_category( $action, $selected_tab );
			$this->getControllerRouter_group( $action, $selected_tab );
			$this->getControllerRouter_property( $action, $selected_tab );
			$this->getControllerRouter_option( $action, $selected_tab );

		}

		return $this->router;
	}







	public static function getCurrentCategory() : Category|null
	{
		return self::$current_category;
	}

	public static function getCurrentParamGroup() : Parametrization_Group|null
	{
		return self::$current_param_group;
	}

	public static function getCurrentParamGroupId() : int
	{
		if(!self::$current_param_group) {
			return 0;
		}

		return self::$current_param_group->getId();
	}

	public static function getCurrentParamProperty() : Parametrization_Property|null
	{
		return self::$current_param_property;
	}

	public static function getCurrentParamPropertyId() : int
	{
		if(!self::$current_param_property) {
			return 0;
		}

		return self::$current_param_property->getId();
	}

	public static function getCurrentParamPropertyOption() : Parametrization_Property_Option|Stencil_Option|null
	{
		return self::$current_param_property_option;
	}

	public static function getCurrentParamPropertyOptionId() : int
	{
		if(!self::$current_param_property_option) {
			return 0;
		}

		return self::$current_param_property_option->getId();
	}

	public static function getCurrentCategoryId() : int
	{
		if(!self::$current_category) {
			return 0;
		}

		return self::$current_category->getId();
	}

	public static function getCurrentNode() : Data_Tree_Node
	{
		return Category::getTree()->getNode( Controller_Main::getCurrentCategoryId() );
	}

	public static function getCurrentAction() : string
	{
		return static::$current_action;
	}


	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		UI_module::initBreadcrumb();


		$tree = Category::getTree();

		$current_node = $tree->getNode( static::getCurrentCategoryId() );

		foreach( $current_node->getPath() as $node ) {

			if(!$node->getId()) {
				continue;
			}

			$label = $node->getLabel();

			if(!$label) {
				$label = '???';
			}

			Navigation_Breadcrumb::addURL( $label, Category::getCategoryEditURL( $node->getId() ) );

		}

		if( static::$current_param_group ) {
			Navigation_Breadcrumb::addURL(
				Tr::_('Group&nbsp;<b>%NAME%</b>', ['NAME'=>static::$current_param_group->getLabel()]),
				Http_Request::currentURI([], ['property_id', 'option_id', 'action', 'type'])
			);
		}

		if( static::$current_param_property ) {
			Navigation_Breadcrumb::addURL(
				Tr::_('Property&nbsp;<b>%NAME%</b>', ['NAME'=>static::$current_param_property->getLabel()]),
				Http_Request::currentURI([], ['option_id', 'action', 'type'])
			);
		}

		if( static::$current_param_property_option ) {
			Navigation_Breadcrumb::addURL(
				Tr::_('Option&nbsp;<b>%NAME%</b>', ['NAME'=>static::$current_param_property_option->getProductDetailLabel()]),
				Http_Request::currentURI([], ['action', 'type'])
			);
		}


		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}

	protected function _getEditTabs() : UI_tabs|null
	{
		if(!static::$current_category) {
			return null;
		}

		if(static::$current_param_property_option) {
			$tabs = [
				'main'   => Tr::_( 'Main data' ),
				'images' => Tr::_( 'Images' ),
			];
		} else {
			if(static::$current_param_property) {
				$tabs = [
					'main'   => Tr::_( 'Main data' ),
					'images' => Tr::_( 'Images' ),
				];
			} else {
				if(static::$current_param_group) {
					$tabs = [
						'main'   => Tr::_( 'Main data' ),
						'images' => Tr::_( 'Images' ),
					];
				} else {
					$tabs = match (static::getCurrentCategory()->getType()) {
						Category::CATEGORY_TYPE_REGULAR => [
							'main'            => Tr::_( 'Main data' ),
							'images'          => Tr::_( 'Images' ),
							'parametrization' => Tr::_( 'Parametrization' ),
							'exports'         => Tr::_( 'Exports' ),
						],
						Category::CATEGORY_TYPE_TOP => [
							'main'   => Tr::_( 'Main data' ),
							'images' => Tr::_( 'Images' ),
						],
						Category::CATEGORY_TYPE_VIRTUAL,
						Category::CATEGORY_TYPE_LINK => [
							'main'   => Tr::_( 'Main data' ),
							'images' => Tr::_( 'Images' ),
							'filter' => Tr::_( 'Filter' ),
						],
					};

				}
			}
		}


		if(!$tabs) {
			return null;
		}

		$tabs = UI::tabs(
			$tabs,
			function($page_id) {
				return Http_Request::currentURI(['page'=>$page_id]);
			},
			Http_Request::GET()->getString('page')
		);

		return $tabs;
	}




	protected function _editCategorySave( bool $reload = true ) : void
	{
		$category = static::getCurrentCategory();
		$category->save();
		Logger::success(
			event: 'category_updated',
			event_message:  'Category '.$category->_getPathName().' ('.$category->getId().') updated',
			context_object_id: $category->getId(),
			context_object_name: $category->_getPathName(),
			context_object_data: $category
		);

		UI_messages::success(
			Tr::_( 'Category <b>%NAME%</b> has been updated', [ 'NAME' => $category->_getPathName() ] )
		);

		if($reload) {
			Http_Headers::reload();
		}

	}




	public function default_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$this->output( 'default' );
	}

	public function whisper_Action() : void
	{
		$GET = Http_Request::GET();

		$only_types = $GET->getString('only_types');
		if($only_types) {
			$only_types = explode(',', $only_types);
		} else {
			$only_types = [];
		}

		$result = Fulltext_Index_Internal_Category::search(
			$GET->getString('whisper'),
			$only_types,
			$GET->getBool('only_active'),
			$GET->getInt('exclude_branch_id')
		);

		$this->view->setVar('result', $result);
		echo $this->view->render('search_whisperer_result');

		Application::end();
	}

	public function set_filter_Action() : void
	{
		$POST = Http_Request::POST();

		Category::setFilter_onlyActive( $POST->getBool('only_active') );
		Category::setFilter_selectedSort( $POST->getString('order_by') );

		Http_Headers::reload([], ['set_filter']);
	}


	public function generate_url_path_part_Action() : void
	{
		$GET = Http_Request::GET();

		AJAX::response([
			'url_path_part' => Shops::generateURLPathPart( $GET->getString('generate_url_path_part'), '', 0, Shops::get( $GET->getString('shop_key') ) )
		]);

		Application::end();
	}


}