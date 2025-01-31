<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
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
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\Admin_Managers_EShopEntity_Edit;
use JetApplication\Admin_Managers_EShopEntity_Listing;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasActivation_Interface;
use JetApplication\EShopEntity_HasActivationByTimePlan_Interface;
use JetApplication\EShopEntity_HasURL_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShop;
use JetApplication\EShops;
use Closure;


class Main extends Application_Module implements Admin_Managers_EShopEntity_Edit
{
	protected null|EShopEntity_Basic|EShopEntity_Admin_WithEShopData_Interface $item = null;
	protected ?Admin_Managers_EShopEntity_Listing $listing = null;
	protected ?UI_tabs $tabs = null;
	protected MVC_View $view;
	protected Closure|null $common_data_fields_renderer = null;
	protected Closure|null $toolbar_renderer = null;
	protected Closure|null $eshop_data_fields_renderer = null;
	protected Closure|null $description_fields_renderer = null;
	
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
	}
	
	protected function render( $script, array $params ) : string
	{
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$this->view->setVar('module', $this);
		
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'listing', $this->listing );
		$this->view->setVar( 'tabs', $this->tabs );
		
		$this->view->setVar( 'toolbar_renderer', $this->toolbar_renderer );
		$this->view->setVar( 'common_data_fields_renderer', $this->common_data_fields_renderer );
		$this->view->setVar( 'eshop_data_fields_renderer', $this->eshop_data_fields_renderer );
		$this->view->setVar( 'description_fields_renderer', $this->description_fields_renderer );
		
		foreach($params as $k=>$v) {
			$this->view->setVar($k, $v);
		}
		
		
		return $this->view->render($script);
	}
	
	
	
	public function init(
		EShopEntity_Basic|EShopEntity_Admin_Interface $item, ?
		Admin_Managers_EShopEntity_Listing            $listing = null,
		?UI_tabs                                      $tabs = null,
		?Closure                                      $common_data_fields_renderer = null,
		?Closure                                      $toolbar_renderer = null,
		?Closure                                      $eshop_data_fields_renderer = null,
		?Closure                                      $description_fields_renderer = null
	): void
	{
		$this->item = $item;
		$this->listing = $listing;
		$this->tabs = $tabs;
		
		$this->common_data_fields_renderer = $common_data_fields_renderer;
		$this->toolbar_renderer = $toolbar_renderer;
		$this->eshop_data_fields_renderer = $eshop_data_fields_renderer;
		$this->description_fields_renderer = $description_fields_renderer;
		
	}
	
	
	public function renderToolbar( ?Form $form = null, ?Closure $toolbar_renderer=null ): string
	{
		$params = [
			'form' => $form
		];
		if($toolbar_renderer) {
			$params['toolbar_renderer'] = $toolbar_renderer;
		}
		
		return $this->render( 'edit/toolbar', $params );
	}
	
	public function renderEditMain( Form $form ): string
	{
		$params = [
			'form' => $form
		];
		
		return $this->render( 'edit/main', $params );
	}
	
	public function renderEditDescription( Form $form ): string
	{
		$params = [
			'form' => $form
		];
		
		return $this->render( 'edit/description', $params );
		
	}
	
	
	public function renderEditImages(): string
	{
		return $this->render( 'edit/images', [] );
	}
	
	public function renderEditImageGallery(): string
	{
		return $this->render( 'edit/image-gallery', [] );
	}
	
	
	public function renderAdd( Form $form ): string
	{
		$params = [
			'form' => $form
		];
		
		return $this->render( 'add', $params );
		
	}
	
	public function renderDeleteConfirm( string $message ): string
	{
		$params = [
			'message' => $message
		];
		
		return $this->render( 'delete/confirm', $params );
		
	}
	
	public function renderDeleteNotPossible(
		string    $message,
		?callable $reason_renderer = null
	): string
	{
		$params = [
			'message' => $message,
			'reason_renderer' => $reason_renderer
		];
		
		return $this->render( 'delete/not-possible', $params );
	}
	
	public function renderShowName( int $id, null|EShopEntity_WithEShopData|EShopEntity_Admin_Interface $item ): string
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
	
	
	public function renderActiveState( EShopEntity_HasActivation_Interface $item ): string
	{
		$params = [
			'item' => $item,
		];
		
		return $this->render( 'active-state', $params );
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
			$params = [
				'form' => $form,
				'eshops' => $eshops
			];
			
			$res .= $this->render('eshop-data-block/inline/start', $params);
			foreach( $eshops as $eshop) {
				$params['eshop'] = $eshop;
				
				$res .= $this->render('eshop-data-block/inline/block-start', $params);
				
				ob_start();
				$renderer( $eshop, $eshop->getKey() );
				$res .= ob_get_clean();
				
				$res .= $this->render('eshop-data-block/inline/block-end', $params);
				
			}
			$res .= $this->render('eshop-data-block/inline/end', $params);
			
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
			
			$params = [
				'tabs' => $eshop_block_tabs,
				'form' => $form,
				'eshops' => $eshops
			];
			
			
			$res .= $this->render('eshop-data-block/tabs/tabs-start', $params);
			foreach( $eshops as $eshop) {
				$params['eshop'] = $eshop;
				$params['tab'] = $eshop_block_tabs->tab($eshop->getKey());
				
				$res .= $this->render('eshop-data-block/tabs/block-start', $params);
				ob_start();
				$renderer( $eshop, $eshop->getKey() );
				$res .= ob_get_clean();
				$res .= $this->render('eshop-data-block/tabs/block-end', $params);
			}
			$res .= $this->render('eshop-data-block/tabs/tabs-end', $params);
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
		
		$params = [
			'tabs' => $tabs,
			'form' => $form,
			'locales' => $locales,
		];
		
		$res .= $this->render('description-block/tabs-start', $params);
		foreach( $locales as $locale) {
			$locale_str = $locale->toString();
			
			$params['locale'] = $locale;
			$params['tab'] = $tabs->tab($locale_str);
			
			$res .= $this->render('description-block/block-start', $params);
			ob_start();
			$renderer( $locale, $locale_str );
			$res .= ob_get_clean();
			$res .= $this->render('description-block/block-end', $params);
		}
		$res .= $this->render('description-block/tabs-end', $params);
		
		return $res;
	}
	
	
	public function renderPreviewButton( EShopEntity_HasURL_Interface|EShopEntity_Basic $item ) : string
	{
		$params = ['item'=>$item];
		
		return $this->render( 'preview-btn', $params );
	}
	
	public function renderEntityActivation(
		EShopEntity_Basic $entity,
		bool              $editable,
		?Closure          $deactivate_url_creator = null,
		?Closure          $activate_url_creator = null,
		?Closure          $activate_completely_url_creator = null,
		?Closure          $deactivate_per_eshop_url_creator = null,
		?Closure          $activate_per_eshop_url_creator = null
	) : string
	{
		if( !$entity instanceof EShopEntity_HasActivation_Interface ) {
			return '';
		}
		
		$params = [
			'entity' => $entity
		];
		
		if($entity instanceof EShopEntity_HasActivationByTimePlan_Interface) {
			return $this->render( 'entity-activation-by-timeplan', $params );
		}
		
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
		
		if( $entity instanceof EShopEntity_WithEShopData ) {
			
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
		
		$res .= $this->render( 'entity-activation/header', $params );
		
		if(!$editable) {
			$res .= $this->render( 'entity-activation/readonly', $params );
		} else {
			$params['deactivate_url'] = $deactivate_url_creator();
			$params['activate_url'] = $activate_url_creator();
			
			if( $entity instanceof EShopEntity_WithEShopData ) {
				$params['activate_completely_url'] = $activate_completely_url_creator();
			}
			
			$res .=  $this->render( 'entity-activation/editable', $params );
		}
		
		if( $entity instanceof EShopEntity_WithEShopData ) {
			foreach(EShops::getListSorted() as $eshop) {
				$params['eshop_data'] = $entity->getEshopData($eshop);
				$params['eshop'] = $eshop;
				
				if(!$editable) {
					$res .= $this->render( 'entity-activation/shop-data/readonly', $params );
				} else {
					$params['deactivate_url'] = $deactivate_per_eshop_url_creator( $eshop );
					$params['activate_url'] = $activate_per_eshop_url_creator( $eshop );
					
					$res .= $this->render( 'entity-activation/shop-data/editable', $params );
				}
			}
			
		}
		
		$res .= $this->render( 'entity-activation/footer', $params );
		
		return $res;
		
	}

	
	public function renderEntityFormCommonFields( Form $form ) : string
	{
		$params = [
			'form' => $form
		];
		
		return $this->render( 'entity-form-common-fields', $params );
	}
	
	
	public function renderEditProducts( EShopEntity_Basic $item ): string
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
	
	public function renderEditFilter( EShopEntity_Basic $item, Form $form ): string
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