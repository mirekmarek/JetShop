<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Edit;

use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Layout;
use Jet\MVC_View;
use Jet\Tr;
use Jet\Translator;
use Jet\UI;
use Jet\UI_tabs;
use JetApplication\Admin_EntityManager_EditTabProvider_EditTab;
use JetApplication\EMail_Sent;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\Admin_Managers_EShopEntity_Edit;
use JetApplication\Admin_Managers_EShopEntity_Listing;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_CanNotBeDeletedReason;
use JetApplication\EShopEntity_HasActivation_Interface;
use JetApplication\EShopEntity_HasActivationByTimePlan_Interface;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasURL_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShop;
use JetApplication\EShops;
use Closure;


class Main extends Admin_Managers_EShopEntity_Edit
{
	protected null|EShopEntity_Basic|EShopEntity_Admin_WithEShopData_Interface $item = null;
	protected ?Admin_Managers_EShopEntity_Listing $listing = null;
	protected ?UI_tabs $tabs = null;
	protected MVC_View $view;
	
	protected Closure|null $add_toolbar_renderer = null;
	
	protected Closure|null $add_common_data_fields_renderer = null;
	protected Closure|null $add_eshop_data_fields_renderer = null;
	protected Closure|null $add_description_fields_renderer = null;
	
	protected Closure|null $edit_toolbar_renderer = null;
	
	protected Closure|null $edit_common_data_fields_renderer = null;
	protected Closure|null $edit_eshop_data_fields_renderer = null;
	protected Closure|null $edit_description_fields_renderer = null;
	
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
	}
	
	protected function render( $script, array $params ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: Tr::COMMON_DICTIONARY,
			action: function() use ($script, $params) {
				$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$this->view->setVar('module', $this);
				
				$this->view->setVar( 'plugins', $this->plugins );
				
				$this->view->setVar( 'item', $this->item );
				$this->view->setVar( 'listing', $this->listing );
				$this->view->setVar( 'tabs', $this->tabs );
				
				
				$this->view->setVar( 'add_toolbar_renderer', $this->add_toolbar_renderer );
				
				$this->view->setVar( 'add_common_data_fields_renderer', $this->add_common_data_fields_renderer );
				$this->view->setVar( 'add_eshop_data_fields_renderer', $this->add_eshop_data_fields_renderer );
				$this->view->setVar( 'add_description_fields_renderer', $this->add_description_fields_renderer );
				
				$this->view->setVar( 'edit_toolbar_renderer', $this->edit_toolbar_renderer );
				
				$this->view->setVar( 'edit_common_data_fields_renderer', $this->edit_common_data_fields_renderer );
				$this->view->setVar( 'edit_eshop_data_fields_renderer', $this->edit_eshop_data_fields_renderer );
				$this->view->setVar( 'edit_description_fields_renderer', $this->edit_description_fields_renderer );
				
				foreach($params as $k=>$v) {
					$this->view->setVar($k, $v);
				}
				
				
				return $this->view->render($script);
			}
		);
	}
	
	
	
	public function init(
		EShopEntity_Basic|EShopEntity_Admin_Interface $item, ?
		Admin_Managers_EShopEntity_Listing            $listing = null,
		?UI_tabs                                      $tabs = null,
		
		?Closure                                      $add_toolbar_renderer = null,
		
		?Closure                                      $add_common_data_fields_renderer = null,
		?Closure                                      $add_eshop_data_fields_renderer = null,
		?Closure                                      $add_description_fields_renderer = null,
		
		?Closure                                      $edit_toolbar_renderer = null,
		
		?Closure                                      $edit_common_data_fields_renderer = null,
		?Closure                                      $edit_eshop_data_fields_renderer = null,
		?Closure                                      $edit_description_fields_renderer = null
	): void
	{
		$this->item = $item;
		$this->listing = $listing;
		$this->tabs = $tabs;
		
		$this->add_toolbar_renderer = $add_toolbar_renderer;
		
		$this->add_common_data_fields_renderer = $add_common_data_fields_renderer;
		$this->add_eshop_data_fields_renderer = $add_eshop_data_fields_renderer;
		$this->add_description_fields_renderer = $add_description_fields_renderer;
		
		$this->edit_toolbar_renderer = $edit_toolbar_renderer;
		
		$this->edit_common_data_fields_renderer = $edit_common_data_fields_renderer;
		$this->edit_eshop_data_fields_renderer = $edit_eshop_data_fields_renderer;
		$this->edit_description_fields_renderer = $edit_description_fields_renderer;
		
	}
	
	
	public function renderToolbar( ?Form $form = null, ?Closure $toolbar_renderer=null ): string
	{
		$params = [
			'form' => $form
		];
		if($toolbar_renderer) {
			$params['edit_toolbar_renderer'] = $toolbar_renderer;
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
	
	/**
	 * @param string $message
	 * @param EShopEntity_CanNotBeDeletedReason[] $reasons
	 * @return string
	 */
	public function renderDeleteNotPossible(
		string  $message,
		array   $reasons
	): string
	{
		$params = [
			'message' => $message,
			'reasons' => $reasons
		];
		
		return $this->render( 'delete/not-possible', $params );
	}
	
	public function renderItemName( int $id, null|EShopEntity_WithEShopData|EShopEntity_Admin_Interface $item ): string
	{
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ($id, $item) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('id', $id);
				
				if($item) {
					$view->setVar('item', $item);
					return $view->render('item-name/known');
				}
				
				return $view->render('item-name/unknown');
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
	
	public function renderEditorTools( EShopEntity_Basic $item ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('item', $item);
		$view->setVar('edit_manager',  $this);
		
		return $view->render('editor-tools');
	}
	public function renderShowStatus( EShopEntity_Status $status ): string
	{
		return $this->render('status', [
			'status' => $status
		]);
	}
	
	public function renderEventHistory( EShopEntity_Basic|EShopEntity_HasEvents_Interface $item, bool $shop_full=false ) : string
	{
		return $this->render('event-history', [
			'shop_full' => $shop_full,
			'history' => $item->getHistory()
		]);
	}
	
	public function renderSentEmails( EShopEntity_Basic $item, bool $shop_full=false ) : string
	{
		return $this->render('sent-emails', [
			'shop_full' => $shop_full,
			'item' => $item
		]);
	}
	
	public function handleShowSentEmail( EShopEntity_Basic $item ) : ?string
	{
		if(($sent_email_id=Http_Request::GET()->getInt('show_sent_email'))) {
			$sent_email = EMail_Sent::load( $sent_email_id );
			if(!$sent_email) {
				Http_Headers::reload(unset_GET_params: ['show_sent_email']);
			}
			MVC_Layout::getCurrentLayout()->setScriptName('dialog');
			
			
			return $this->render('sent-email', [
				'sent_email' => $sent_email
			]);
		}
		
		return false;
	}
	
	/**
	 * @param EShopEntity_Status_PossibleFutureStatus[] $future_statuses
	 * @return string
	 */
	public function renderEntitySetStatusButtons( array $future_statuses ) : string
	{
		return $this->render('set-status/buttons', [
			'future_statuses' => $future_statuses
		]);
	}
	
	/**
	 * @param EShopEntity_Status_PossibleFutureStatus[] $future_statuses
	 * @param Form[] $forms
	 * @return string
	 */
	public function renderEntitySetStatusDialogs( array $future_statuses, array $forms ) : string
	{
		return $this->render('set-status/dialogs', [
			'future_statuses' => $future_statuses,
			'forms' => $forms
		]);
	}
	
	public function renderEntityForceStatusButton(): string
	{
		return $this->render('force-status/button', [
		]);
	}
	
	public function renderEntityForceStatusDialog( Form $form ): string
	{
		return $this->render('force-status/dialog', [
			'form' => $form
		]);
	}
	
	public function renderProvidetTab( Admin_EntityManager_EditTabProvider_EditTab $tab ): string
	{
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ($tab) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$view->setVar('provided_tab_output', $tab->handle() );
				$view->setVar('tabs', $this->tabs);
				$view->setVar('edit_manager',  $this);
				
				return $view->render('edit/providet-tab');
			}
		);
	}
}