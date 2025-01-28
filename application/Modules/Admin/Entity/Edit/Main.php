<?php
/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Entity\Edit;

use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Translator;
use Jet\UI;
use Jet\UI_tabs;
use JetApplication\Entity_Admin_Interface;
use JetApplication\Entity_Admin_WithEShopData_Interface;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Entity_Basic;
use JetApplication\Entity_HasActivation_Interface;
use JetApplication\Entity_HasURL_Interface;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;
use JetApplication\EShops;
use Closure;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Entity_Edit
{
	protected Entity_Basic|Entity_Admin_WithEShopData_Interface $item;
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
		Entity_Basic|Entity_Admin_Interface $item,
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
		?callable $eshop_data_fields_renderer = null,
		?callable $description_fields_renderer = null
	): string
	{
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'listing', $this->listing );
		$this->view->setVar( 'tabs', $this->tabs );
		$this->view->setVar( 'form', $this->item->getEditForm() );
		
		$this->view->setVar( 'toolbar_renderer', $toolbar_renderer );
		
		$this->view->setVar( 'common_data_fields_renderer', $common_data_fields_renderer );
		$this->view->setVar( 'eshop_data_fields_renderer', $eshop_data_fields_renderer );
		$this->view->setVar( 'description_fields_renderer', $description_fields_renderer );
		
		
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
		?callable $eshop_data_fields_renderer = null,
		?callable $description_fields_renderer = null
	): string
	{
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'tabs', $this->tabs );
		$this->view->setVar( 'form', $this->item->getAddForm() );
		$this->view->setVar( 'common_data_fields_renderer', $common_data_fields_renderer );
		$this->view->setVar( 'eshop_data_fields_renderer', $eshop_data_fields_renderer );
		$this->view->setVar( 'description_fields_renderer', $description_fields_renderer );
		
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
	
	public function renderShowName( int $id, null|Entity_WithEShopData|Entity_Admin_Interface $item ): string
	{
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ($id, $item) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('id', $id);
				
				if($item) {
					$view->setVar('item', $item);
					return $view->render('show-name/known');
				}
				
				return $view->render('show-name/unknown');
			}
		);
	}
	
	
	public function renderActiveState( Entity_HasActivation_Interface $item ): string
	{
		$this->view->setVar( 'item', $item );
		return $this->render( 'active-state' );
	}
	
	
	public function renderShopDataBlocks(
		?Form $form=null,
		?array $eshops=null,
		bool $inline_mode=false,
		?callable $renderer=null
	) : string
	{
		if($eshops===null) {
			$eshops = EShops::getListSorted();
		}
		
		if(count($eshops)<2) {
			foreach( $eshops as $eshop) {
				ob_start();
				$renderer( $eshop, $eshop->getKey() );
				return ob_get_clean();
			}

		}
		
		$res = '';
		
		if($inline_mode) {
			$this->view->setVar('form', $form);
			$this->view->setVar('eshops', $eshops);
			
			$res .= $this->render('eshop-data-block/inline/start');
			foreach( $eshops as $eshop) {
				$this->view->setVar('eshop', $eshop );
				
				$res .= $this->render('eshop-data-block/inline/block-start');
				ob_start();
				$renderer( $eshop, $eshop->getKey() );
				$res .= ob_get_clean();
				$res .= $this->render('eshop-data-block/inline/block-end');
			}
			$res .= $this->render('eshop-data-block/inline/end');
			
		} else {
			
			$tabs = [];
			foreach( $eshops as $tab_eshop ) {
				$tabs[$tab_eshop->getKey()] = UI::flag( $tab_eshop->getLocale() ).' '.$tab_eshop->getName();
			}
			
			
			$selected_tab_id = $_COOKIE['selected_eshop_block_key']??null;
			
			$eshop_block_tabs = UI::tabsJS(
				id: 'set_eshop',
				tabs: $tabs,
				selected_tab_id: $selected_tab_id
			);
			
			$this->view->setVar('tabs', $eshop_block_tabs);
			$this->view->setVar('form', $form);
			$this->view->setVar('eshops', $eshops);
			
			$res .= $this->render('eshop-data-block/tabs/tabs-start');
			foreach( $eshops as $eshop) {
				$this->view->setVar('eshop', $eshop );
				$this->view->setVar('tab', $eshop_block_tabs->tab($eshop->getKey()) );
				
				$res .= $this->render('eshop-data-block/tabs/block-start');
				ob_start();
				$renderer( $eshop, $eshop->getKey() );
				$res .= ob_get_clean();
				$res .= $this->render('eshop-data-block/tabs/block-end');
			}
			$res .= $this->render('eshop-data-block/tabs/tabs-end');
		}
		
		return $res;
	}
	
	public function renderDescriptionBlocks(
		?Form $form=null,
		?array $locales=null,
		?callable $renderer=null
	) : string
	{
		if($locales===null) {
			$locales = EShops::getAvailableLocales();
		}
		
		if(count($locales)<2) {
			foreach( $locales as $locale) {
				ob_start();
				$renderer( $locale, $locale->toString() );
				return ob_get_clean();
			}
		}
		
		$res = '';
		
		$tabs = [];
		foreach( $locales as $locale ) {
			$tabs[$locale->toString()] = UI::flag( $locale ).' '.$locale->getLanguageName( $locale );
		}
		
		
		$selected_tab_id = $_COOKIE['selected_description_locale']??null;
		
		$tabs = UI::tabsJS(
			id: 'set_eshop',
			tabs: $tabs,
			selected_tab_id: $selected_tab_id
		);
		
		$this->view->setVar('tabs', $tabs);
		$this->view->setVar('form', $form);
		$this->view->setVar('locales', $locales);
		
		$res .= $this->render('description-block/tabs-start');
		foreach( $locales as $locale) {
			$locale_str = $locale->toString();
			
			$this->view->setVar('locale', $locale );
			$this->view->setVar('tab', $tabs->tab($locale_str) );
			
			$res .= $this->render('description-block/block-start');
			ob_start();
			$renderer( $locale, $locale_str );
			$res .= ob_get_clean();
			$res .= $this->render('description-block/block-end');
		}
		$res .= $this->render('description-block/tabs-end');
		
		return $res;
	}
	
	
	public function renderPreviewButton( Entity_HasURL_Interface|Entity_Basic $item ) : string
	{
		$this->view->setVar('item', $item);
		
		return $this->render( 'preview-btn' );
	}
	
	public function renderEntityActivation(
		Entity_Basic $entity,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null,
		?Closure $activate_completely_url_creator = null,
		?Closure $deactivate_per_eshop_url_creator = null,
		?Closure $activate_per_eshop_url_creator = null
	) : string
	{
		if($this->activation_disabled) {
			return '';
		}
		
		$this->view->setVar('entity', $entity);
		
		if(!$deactivate_url_creator) {
			$deactivate_url_creator = function() : string {
				return Http_Request::currentURI(['deactivate_entity'=>1]);
			};
		}
		if(!$activate_url_creator) {
			$activate_url_creator = function() : string {
				return Http_Request::currentURI(['activate_entity'=>1]);
			};
		}
		
		if( $entity instanceof Entity_WithEShopData ) {
			
			if(!$activate_completely_url_creator) {
				$activate_completely_url_creator = function() : string {
					return Http_Request::currentURI(['activate_entity_completely'=>1]);
				};
			}
			
			if(!$deactivate_per_eshop_url_creator) {
				$deactivate_per_eshop_url_creator = function( EShop $eshop ) : string {
					return Http_Request::currentURI(['deactivate_entity_eshop_data'=>$eshop->getKey()]);
				};
			}
			
			if( !$activate_per_eshop_url_creator ) {
				$activate_per_eshop_url_creator = function( EShop $eshop ) : string {
					return Http_Request::currentURI(['activate_entity_eshop_data'=>$eshop->getKey()]);
				};
			}
		}
		
		
		
		$res = '';
		
		$res .= $this->render( 'entity-activation/header' );
		
		if(!$editable) {
			$res .= $this->render( 'entity-activation/readonly' );
			
		} else {
			$this->view->setVar('deactivate_url', $deactivate_url_creator() );
			$this->view->setVar('activate_url',  $activate_url_creator() );
			
			if( $entity instanceof Entity_WithEShopData ) {
				$this->view->setVar('activate_completely_url', $activate_completely_url_creator() );
			}
			
			$res .=  $this->render( 'entity-activation/editable' );
		}
		
		if( $entity instanceof Entity_WithEShopData ) {
			foreach(EShops::getListSorted() as $eshop) {
				$this->view->setVar('eshop_data', $entity->getEshopData($eshop));
				$this->view->setVar('eshop', $eshop);
				
				
				if(!$editable) {
					$res .= $this->render( 'entity-activation/shop-data/readonly' );
				} else {
					
					$this->view->setVar('deactivate_url', $deactivate_per_eshop_url_creator( $eshop ) );
					$this->view->setVar('activate_url', $activate_per_eshop_url_creator( $eshop ) );
					
					$res .= $this->render( 'entity-activation/shop-data/editable' );
				}
			}
			
		}
		
		$res .= $this->render( 'entity-activation/footer' );
		
		return $res;
		
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
	
	
	public function renderEditProducts( Entity_Basic $item ): string
	{
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ($item) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$view->setVar('item', $item);
				$view->setVar('tabs', $this->tabs);
				$view->setVar('edit_manager',  $this);
				
				return $view->render('edit/products');
			}
		);
	}
	
	public function renderEditFilter( Entity_Basic $item, Form $form ): string
	{
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ($item, $form) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$view->setVar('filter_form', $form);
				$view->setVar('item', $item);
				$view->setVar('tabs', $this->tabs);
				$view->setVar('edit_manager',  $this);
				return $view->render('edit/filter');
			}
		);
	}
	
	
}