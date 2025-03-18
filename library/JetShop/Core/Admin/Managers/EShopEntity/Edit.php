<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Form;
use Jet\UI_tabs;
use JetApplication\Admin_EntityManager_EditTabProvider_EditTab;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\Admin_Managers_EShopEntity_Listing;
use JetApplication\EShopEntity_CanNotBeDeletedReason;
use JetApplication\EShopEntity_HasActivation_Interface;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasURL_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_WithEShopData;
use Closure;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Entity editor',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_EShopEntity_Edit extends Application_Module
{
	abstract public function init(
		EShopEntity_Basic|EShopEntity_Admin_Interface $item,
		?Admin_Managers_EShopEntity_Listing           $listing = null,
		?UI_tabs                                      $tabs = null,
		?Closure                                      $common_data_fields_renderer = null,
		?Closure                                      $toolbar_renderer = null,
		?Closure                                      $eshop_data_fields_renderer = null,
		?Closure                                      $description_fields_renderer = null
	) : void;
	
	abstract public function renderToolbar( ?Form $form = null, ?Closure $toolbar_renderer=null  ): string;
	
	abstract public function renderEditMain( Form $form ): string;
	
	abstract public function renderEditDescription( Form $form ) : string;
	
	abstract public function renderEditImages(): string;
	
	abstract public function renderEditImageGallery(): string;
	
	abstract public function renderAdd( Form $form ): string;
	
	abstract public function renderDeleteConfirm( string $message ): string;
	
	/**
	 * @param string $message
	 * @param EShopEntity_CanNotBeDeletedReason[] $reasons
	 * @return string
	 */
	abstract public function renderDeleteNotPossible( string $message, array $reasons ): string;
	
	abstract public function renderItemName(
		int $id,
		null|EShopEntity_WithEShopData|EShopEntity_Admin_Interface $item
	): string;
	
	abstract public function renderActiveState(
		EShopEntity_HasActivation_Interface $item
	): string;
	
	
	abstract public function renderShopDataBlocks(
		?Form     $form=null,
		?array    $eshops=null,
		bool      $inline_mode=false,
		?callable $renderer=null
	) : string;
	
	abstract public function renderDescriptionBlocks(
		?Form $form=null,
		?array $locales=null,
		?callable $renderer=null
	) : string;
	
	
	abstract public function renderPreviewButton( EShopEntity_Basic|EShopEntity_HasURL_Interface $item ) : string;
	
	abstract public function renderEntityActivation(
		EShopEntity_Basic $entity,
		bool              $editable,
		?Closure          $deactivate_url_creator = null,
		?Closure          $activate_url_creator = null,
		?Closure          $activate_completely_url_creator = null,
		?Closure          $deactivate_per_eshop_url_creator = null,
		?Closure          $activate_per_eshop_url_creator = null
	) : string;
	
	abstract public function renderEntityFormCommonFields( Form $form ) : string;
	
	abstract public function renderEditProducts( EShopEntity_Basic $item ): string;
	
	abstract public function renderEditFilter( EShopEntity_Basic $item, Form $form ): string;
	
	abstract public function renderEditorTools( EShopEntity_Basic $item ) : string;
	
	abstract public function renderShowStatus( EShopEntity_Status $status ) : string;
	
	abstract public function renderEventHistory( EShopEntity_Basic|EShopEntity_HasEvents_Interface $item ) : string;
	
	abstract public function renderSentEmails( EShopEntity_Basic $item ) : string;
	
	abstract public function handleShowSentEmail( EShopEntity_Basic $item ) : ?string;
	
	/**
	 * @param EShopEntity_Status_PossibleFutureStatus[] $future_statuses
	 * @return string
	 */
	abstract public function renderEntitySetStatusButtons( array $future_statuses ) : string;
	
	/**
	 * @param EShopEntity_Status_PossibleFutureStatus[] $future_statuses
	 * @param Form[] $forms
	 * @return string
	 */
	abstract public function renderEntitySetStatusDialogs( array $future_statuses, array $forms ) : string;
	
	abstract public function renderEntityForceStatusButton() : string;
	
	/**
	 * @param Form $form
	 * @return string
	 */
	abstract public function renderEntityForceStatusDialog( Form $form ) : string;
	
	abstract public function renderProvidetTab( Admin_EntityManager_EditTabProvider_EditTab $tab ) : string;
	
}