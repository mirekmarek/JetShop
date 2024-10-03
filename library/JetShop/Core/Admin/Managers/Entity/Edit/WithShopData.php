<?php
namespace JetShop;

use Jet\Form;
use Closure;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Entity_WithShopData;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Shops_Shop;

interface Core_Admin_Managers_Entity_Edit_WithShopData extends Admin_Managers_Entity_Edit
{
	public function renderEditMain(
		?callable $common_data_fields_renderer = null,
		?callable $toolbar_renderer = null,
		?callable $shop_data_fields_renderer = null
	): string;
	
	public function renderEditImages(
		?callable $toolbar_renderer = null
	): string;
	
	public function renderAdd(
		?callable $common_data_fields_renderer = null,
		?callable $shop_data_fields_renderer = null
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
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $entity
	): string;
	
	public function showActiveState(
		int $id, Entity_WithShopData|Admin_Entity_WithShopData_Interface $entity
	): string;
	public function renderActiveState(
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $item
	): string;
	
	
	public function renderShopDataBlocks(
		?Form $form=null,
		?array $shops=null,
		bool $inline_mode=false,
		?callable $renderer=null
	) : void;
	
	public function renderPreviewButton( Entity_WithShopData $item ) : string;
	
	public function renderEntityActivation(
		Entity_WithShopData $entity,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null,
		?Closure $activate_completely_url_creator = null
	) : string;
	

	public function renderEntityShopDataActivation(
		Entity_WithShopData $entity,
		Shops_Shop $shop,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null
	) : string;
	
	public function renderEntityFormCommonFields( Form $form ) : string;
	
	
	public function getActivationDisabled(): bool;
	
	public function setActivationDisabled( bool $activation_disabled ): void;
	
	public function getTimerDisabled(): bool;
	
	public function setTimerDisabled( bool $timer_disabled ): void;
	
}