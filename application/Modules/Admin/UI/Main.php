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
use JetApplication\Entity_Basic;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

use Closure;

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
			$page->getURL()
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
	
	
	public function renderShopDataBlocks( ?Form $form=null, ?array $shops=null, bool $inline_mode=false, ?callable $renderer=null ) : void
	{
		if($shops===null) {
			$shops = Shops::getListSorted();
		}
		
		if(count($shops)<2) {
			foreach($shops as $shop) {
				$renderer( $shop, $shop->getKey() );
			}
			
			return;
		}
		
		if($inline_mode) {
			$this->view->setVar('form', $form);
			$this->view->setVar('shops', $shops);
			
			foreach($shops as $shop) {
				$this->view->setVar('shop', $shop );
				
				echo $this->render('shop-data-block/inline/block-start');
				$renderer( $shop, $shop->getKey() );
				echo $this->render('shop-data-block/inline/block-end');
			}
			
		} else {
			
			$tabs = [];
			foreach( $shops as $tab_shop ) {
				$tabs[$tab_shop->getKey()] = UI::flag( $tab_shop->getLocale() ).' '.$tab_shop->getShopName();
			}
			
			$shop_block_tabs = UI::tabsJS('set_shop', $tabs);
			
			$this->view->setVar('tabs', $shop_block_tabs);
			$this->view->setVar('form', $form);
			$this->view->setVar('shops', $shops);
			
			echo $this->render('shop-data-block/tabs/tabs-start');
			foreach($shops as $shop) {
				$this->view->setVar('shop', $shop );
				$this->view->setVar('tab', $shop_block_tabs->tab($shop->getKey()) );
				
				echo $this->render('shop-data-block/tabs/block-start');
				$renderer( $shop, $shop->getKey() );
				echo $this->render('shop-data-block/tabs/block-end');
			}
			echo $this->render('shop-data-block/tabs/tabs-end');
		}
		
	}
	
	
	public function renderSelectEntityWidget(
		string $name,
		string $caption,
		string $on_select,
		string $object_class,
		string|array|null $object_type_filter,
		?bool $object_is_active_filter,
		?string $selected_entity_title,
		?string $selected_entity_edit_URL
	) : string
	{
		$this->view->setVar('name', $name);
		$this->view->setVar('caption', $caption);
		$this->view->setVar('on_select', $on_select);
		$this->view->setVar('object_class', $object_class);
		$this->view->setVar('object_type_filter', $object_type_filter);
		$this->view->setVar('object_is_active_filter', $object_is_active_filter);
		$this->view->setVar('selected_entity_title', $selected_entity_title);
		$this->view->setVar('selected_entity_edit_URL', $selected_entity_edit_URL);
		
		return $this->render('select-entity-widget');
	}
	
	public function renderEntityActivation(
		Entity_Basic $entity,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null,
		?Closure $activate_completely_url_creator = null
		
	) : string
	{
		$this->view->setVar('entity', $entity);
		
		if(!$editable) {
			return $this->render( 'entity-activation/readonly' );
		}
		
		if(!$deactivate_url_creator) {
			$deactivate_url_creator = function () : string {
				return Http_Request::currentURI(['deactivate_entity'=>1]);
			};
		}
		if(!$activate_url_creator) {
			$activate_url_creator = function () : string {
				return Http_Request::currentURI(['activate_entity'=>1]);
			};
		}
		if(!$activate_completely_url_creator) {
			$activate_completely_url_creator = function () : string {
				return Http_Request::currentURI(['activate_entity_completely'=>1]);
			};
		}
		
		$this->view->setVar('deactivate_url', $deactivate_url_creator() );
		$this->view->setVar('activate_url', $activate_url_creator() );
		
		if($entity instanceof Entity_WithShopData) {
			$this->view->setVar('activate_completely_url', $activate_completely_url_creator() );
		}
		
		
		
		return $this->render( 'entity-activation/editable' );
		
		
	}
	
	public function renderEntityShopDataActivation(
		Entity_WithShopData $entity,
		Shops_Shop $shop,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null
	) : string
	{
		
		
		$this->view->setVar('entity', $entity);
		$this->view->setVar('shop_data', $entity->getShopData($shop));
		$this->view->setVar('shop', $shop);
		
		if(!$editable) {
			return $this->render( 'entity-shop-data-activation/readonly' );
		}
		
		
		if(!$deactivate_url_creator) {
			$deactivate_url_creator = function () use ($shop) : string {
				return Http_Request::currentURI(['deactivate_entity_shop_data'=>$shop->getKey()]);
			};
		}
		if(!$activate_url_creator) {
			$activate_url_creator = function () use ($shop) : string {
				return Http_Request::currentURI(['activate_entity_shop_data'=>$shop->getKey()]);
			};
		}
		
		$this->view->setVar('deactivate_url', $deactivate_url_creator() );
		$this->view->setVar('activate_url', $activate_url_creator() );
		
		return $this->render( 'entity-shop-data-activation/editable' );
	}
	
	public function renderEntityFormCommonFields( Form $form ) : string
	{
		$this->view->setVar('form', $form);
		
		return $this->render( 'entity-form-common-fields' );
		
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