<?php
namespace JetShop;

use Closure;
use Jet\Form;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Entity_WithEShopData;
use JetApplication\Admin_Entity_WithEShopData_Interface;

interface Core_Admin_Managers_Entity_Edit_WithEShopData extends Admin_Managers_Entity_Edit
{
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
		Entity_WithEShopData|Admin_Entity_WithEShopData_Interface $entity
	): string;
	
	public function showActiveState(
		int $id, Entity_WithEShopData|Admin_Entity_WithEShopData_Interface $entity
	): string;
	public function renderActiveState(
		Entity_WithEShopData|Admin_Entity_WithEShopData_Interface $item
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
	
	
	public function renderPreviewButton( Entity_WithEShopData $item ) : string;
	
	public function renderEntityActivation(
		Entity_WithEShopData $entity,
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
	
}