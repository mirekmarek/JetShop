<?php
namespace JetApplication;


use Jet\Form;
use Jet\UI_tabs;

interface Admin_Managers_Entity_Edit_WithShopData
{
	public function renderToolbar(
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $item,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?Form                                                   $form = null,
		?callable                                               $toolbar_renderer=null
	) : string;
	
	
	public function renderEditMain(
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $item,
		?UI_tabs                                                $tabs=null,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?callable                                               $common_data_fields_renderer=null,
		?callable                                               $shop_data_fields_renderer=null,
		?callable                                               $toolbar_renderer=null
	) : string;
	
	public function renderEditImages(
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $item,
		?UI_tabs                                                $tabs=null,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?callable                                               $toolbar_renderer=null
	) : string;
	
	public function renderAdd(
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $item,
		?UI_tabs $tabs=null,
		?callable $common_data_fields_renderer=null,
		?callable $shop_data_fields_renderer=null
	) : string;
	
	public function renderDeleteConfirm(
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $item,
		string $message
	) : string;

	public function renderDeleteNotPossible(
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $item,
		string $message,
		?callable $reason_renderer=null
	): string;
	
	public function renderActiveState(
		Entity_WithShopData|Admin_Entity_WithShopData_Interface $item
	) : string;
	
}