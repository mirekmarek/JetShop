<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\UI;

use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Navigation_Breadcrumb;
use Jet\MVC;
use Jet\Session;
use Jet\Translator;
use Jet\UI;

use JetApplication\Admin_Managers_UI;
use JetApplication\Shops;


/**
 *
 */
class Main extends Application_Module implements Admin_Managers_UI
{

	protected MVC_View $view;
	
	public function initBreadcrumb() : void
	{
		$page = MVC::getPage();

		Navigation_Breadcrumb::reset();

		Navigation_Breadcrumb::addURL(
			UI::icon( $page->getIcon() ).'&nbsp;&nbsp;'.$page->getBreadcrumbTitle(),
			Http_Request::currentURI(unset_GET_params: ['id', 'action', 'page'])
		);

	}
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
	
	
	protected function render( $script ) : string
	{
		$res = '';
		
		Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use (&$res, $script) {
				$res = $this->view->render($script);
			}
		);
		
		return $res;
	}
	
	
	
	
	public function renderSelectEntityWidget(
		string $name,
		string $caption,
		string $on_select,
		string $entity_type,
		string|array|null $object_type_filter,
		?bool $object_is_active_filter,
		?string $selected_entity_title,
		?string $selected_entity_edit_URL
	) : string
	{
		$this->view->setVar('name', $name);
		$this->view->setVar('caption', $caption);
		$this->view->setVar('on_select', $on_select);
		$this->view->setVar('entity_type', $entity_type);
		$this->view->setVar('object_type_filter', $object_type_filter);
		$this->view->setVar('object_is_active_filter', $object_is_active_filter);
		$this->view->setVar('selected_entity_title', $selected_entity_title);
		$this->view->setVar('selected_entity_edit_URL', $selected_entity_edit_URL);
		
		return $this->render('select-entity-widget');
	}
	
	
	public function renderEntityToolbar( Form $form, ?callable $buttons_renderer=null ) : string
	{
		$this->view->setVar('form', $form);
		$this->view->setVar('buttons_renderer', $buttons_renderer);
		
		return $this->render( 'entity-toolbar' );
	}
	
	
	public const CURR_SHOP_SESSION = 'current_shop';
	public const CURR_SHOP_SESSION_KEY = 'key';
	public const CURR_SHOP_GET_PARAM = 'select_shop';
	

	public function handleCurrentPreferredShop() : void
	{
		$all_shops = array_keys(Shops::getList());
		$default_shop = Shops::getDefault();
		
		$session = new Session( static::CURR_SHOP_SESSION );
		$current_shop_key = $session->getValue(static::CURR_SHOP_SESSION_KEY, '');
		if(!in_array($current_shop_key, $all_shops)) {
			$current_shop_key = $default_shop->getKey();
			$session->setValue(static::CURR_SHOP_SESSION_KEY, $current_shop_key);
		}
		
		
		$GET = Http_Request::GET();
		if($GET->exists(static::CURR_SHOP_GET_PARAM)) {
			$current_shop_key = $GET->getString(
				key:static::CURR_SHOP_GET_PARAM,
				default_value: $default_shop->getKey(),
				valid_values: $all_shops
			);
			
			$session->setValue(static::CURR_SHOP_SESSION_KEY, $current_shop_key);
			
			Http_Headers::reload(unset_GET_params: [static::CURR_SHOP_GET_PARAM]);
		}
		
		Shops::setCurrent( Shops::get($current_shop_key) );
	}
	
	public function renderMainMenu() : string
	{
		return $this->render( 'main-menu' );
	}
	
}