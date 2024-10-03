<?php
/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Entity\Edit\WithShopData;

use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\UI;
use Jet\UI_tabs;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Managers_Entity_Edit_WithShopData;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Entity_Basic;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use Closure;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Entity_Edit_WithShopData
{
	protected Entity_Basic|Admin_Entity_Interface $item;
	protected ?Admin_Managers_Entity_Listing $listing = null;
	protected ?UI_tabs $tabs = null;
	protected MVC_View $view;
	
	protected bool $activation_disabled = false;
	protected bool $timer_disabled = false;
	
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$this->view->setVar('module', $this);
	}
	
	protected function render( $script ) : string
	{
		return $this->view->render($script);
	}
	
	
	
	public function init(
		Entity_Basic|Admin_Entity_Interface $item,
		?Admin_Managers_Entity_Listing      $listing = null,
		?UI_tabs                            $tabs = null
	): void
	{
		$this->item = $item;
		$this->listing = $listing;
		$this->tabs = $tabs;
	}
	
	
	public function renderToolbar(
		?Form     $form = null,
		?callable $toolbar_renderer = null
	): string
	{
		
		
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'listing', $this->listing );
		$this->view->setVar( 'form', $form );
		
		$this->view->setVar( 'toolbar_renderer', $toolbar_renderer );
		
		return $this->render( 'edit/toolbar' );
		
	}
	
	public function renderEditMain(
		?callable $common_data_fields_renderer = null,
		?callable $toolbar_renderer = null,
		?callable $shop_data_fields_renderer = null
	): string
	{
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'listing', $this->listing );
		$this->view->setVar( 'tabs', $this->tabs );
		$this->view->setVar( 'form', $this->item->getEditForm() );
		$this->view->setVar( 'common_data_fields_renderer', $common_data_fields_renderer );
		$this->view->setVar( 'shop_data_fields_renderer', $shop_data_fields_renderer );
		$this->view->setVar( 'toolbar_renderer', $toolbar_renderer );
		
		return $this->render( 'edit/main' );
		
	}
	
	public function renderEditImages(
		?callable $toolbar_renderer = null
	): string
	{
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'listing', $this->listing );
		$this->view->setVar( 'tabs', $this->tabs );
		$this->view->setVar( 'toolbar_renderer', $toolbar_renderer );
		
		return $this->render( 'edit/images' );
		
	}
	
	public function renderAdd(
		?callable $common_data_fields_renderer = null,
		?callable $shop_data_fields_renderer = null
	): string
	{
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'tabs', $this->tabs );
		$this->view->setVar( 'form', $this->item->getAddForm() );
		$this->view->setVar( 'common_data_fields_renderer', $common_data_fields_renderer );
		$this->view->setVar( 'shop_data_fields_renderer', $shop_data_fields_renderer );
		
		return $this->render( 'add' );
		
	}
	
	public function renderDeleteConfirm(
		string $message
	): string
	{
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'message', $message );
		
		return $this->render( 'delete/confirm' );
		
	}
	
	public function renderDeleteNotPossible(
		string    $message,
		?callable $reason_renderer = null
	): string
	{
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'message', $message );
		$this->view->setVar( 'reason_renderer', $reason_renderer );
		
		return $this->render( 'delete/not-possible' );
	}
	
	public function renderShowName( int $id, Entity_WithShopData|Admin_Entity_WithShopData_Interface $entity ): string
	{
		$item = $entity::get( $id );
		
		$this->view->setVar( 'id', $id );
		
		if( $item ) {
			$this->view->setVar( 'item', $item );
			return $this->render( 'show-name/known' );
		}
		
		return $this->render( 'show-name/unknown' );
	}
	
	public function showActiveState( int $id, Entity_WithShopData|Admin_Entity_WithShopData_Interface $entity ): string
	{
		$item = $entity::get( $id );
		
		$this->view->setVar( 'id', $id );
		
		if( $item ) {
			$this->view->setVar( 'item', $item );
			return $this->render( 'active-state' );
		}
		
		return '';
	}
	
	public function renderActiveState( Entity_WithShopData|Admin_Entity_WithShopData_Interface $item ): string
	{
		$this->view->setVar( 'item', $item );
		return $this->render( 'active-state' );
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
			
			echo $this->render('shop-data-block/inline/start');
			foreach($shops as $shop) {
				$this->view->setVar('shop', $shop );
				
				echo $this->render('shop-data-block/inline/block-start');
				$renderer( $shop, $shop->getKey() );
				echo $this->render('shop-data-block/inline/block-end');
			}
			echo $this->render('shop-data-block/inline/end');
			
		} else {
			
			$tabs = [];
			foreach( $shops as $tab_shop ) {
				$tabs[$tab_shop->getKey()] = UI::flag( $tab_shop->getLocale() ).' '.$tab_shop->getShopName();
			}
			
			
			$selected_tab_id = $_COOKIE['selected_shop_block_key']??null;
			
			$shop_block_tabs = UI::tabsJS(
				id: 'set_shop',
				tabs: $tabs,
				selected_tab_id: $selected_tab_id
			);
			
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
	
	public function renderPreviewButton( Entity_WithShopData $item ) : string
	{
		$this->view->setVar('item', $item);
		
		return $this->render( 'preview-btn' );
	}
	
	public function renderEntityActivation(
		Entity_WithShopData $entity,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null,
		?Closure $activate_completely_url_creator = null
	) : string
	{
		if($this->activation_disabled) {
			return '';
		}
		
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
		$this->view->setVar('activate_completely_url', $activate_completely_url_creator() );
		
		
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
		if($this->activation_disabled) {
			return '';
		}
		
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
	
	public function getActivationDisabled(): bool
	{
		return $this->activation_disabled;
	}
	
	public function setActivationDisabled( bool $activation_disabled ): void
	{
		$this->activation_disabled = $activation_disabled;
	}
	
	public function getTimerDisabled(): bool
	{
		return $this->timer_disabled;
	}
	
	public function setTimerDisabled( bool $timer_disabled ): void
	{
		$this->timer_disabled = $timer_disabled;
	}
	
	
}