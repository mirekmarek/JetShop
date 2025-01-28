<?php
namespace JetShop;

use Jet\Form;
use Jet\UI_tabs;
use JetApplication\Entity_Basic;
use JetApplication\Entity_Admin_Interface;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Entity_HasActivation_Interface;
use JetApplication\Entity_HasURL_Interface;
use JetApplication\Entity_WithEShopData;
use Closure;

interface Core_Admin_Managers_Entity_Edit
{
	public function init(
		Entity_Basic|Entity_Admin_Interface $item,
		?Admin_Managers_Entity_Listing      $listing = null,
		?UI_tabs                            $tabs = null
	) : void;
	
	public function renderToolbar(
		?Form                               $form = null,
		?callable                           $toolbar_renderer = null
	): string;
	
	public function renderEditMain(
		?callable $common_data_fields_renderer = null,
		?callable $toolbar_renderer = null,
		?callable $eshop_data_fields_renderer = null,
		?callable $description_fields_renderer = null
	): string;
	
	public function renderEditImages(
		?callable $toolbar_renderer = null
	): string;
	
	public function renderAdd(
		?callable $common_data_fields_renderer = null,
		?callable $eshop_data_fields_renderer = null,
		?callable $description_fields_renderer = null
	): string;
	
	public function renderDeleteConfirm(
		string $message
	): string;
	
	public function renderDeleteNotPossible(
		string    $message,
		?callable $reason_renderer = null
	): string;
	
	public function renderShowName(
		int $id,
		null|Entity_WithEShopData|Entity_Admin_Interface $item
	): string;
	
	public function renderActiveState(
		Entity_HasActivation_Interface $item
	): string;
	
	
	public function renderShopDataBlocks(
		?Form     $form=null,
		?array    $eshops=null,
		bool      $inline_mode=false,
		?callable $renderer=null
	) : string;
	
	public function renderDescriptionBlocks(
		?Form $form=null,
		?array $locales=null,
		?callable $renderer=null
	) : string;
	
	
	public function renderPreviewButton( Entity_Basic|Entity_HasURL_Interface $item ) : string;
	
	public function renderEntityActivation(
		Entity_Basic $entity,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null,
		?Closure $activate_completely_url_creator = null,
		?Closure $deactivate_per_eshop_url_creator = null,
		?Closure $activate_per_eshop_url_creator = null
	) : string;
	
	public function renderEntityFormCommonFields( Form $form ) : string;
	
	
	public function getActivationDisabled(): bool;
	
	public function setActivationDisabled( bool $activation_disabled ): void;
	
	public function getTimerDisabled(): bool;
	
	public function setTimerDisabled( bool $timer_disabled ): void;
	
	public function renderEditProducts( Entity_Basic $item ): string;
	
	public function renderEditFilter( Entity_Basic $item, Form $form ): string;
	
}