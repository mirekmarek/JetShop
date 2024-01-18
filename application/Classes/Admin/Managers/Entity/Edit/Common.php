<?php
namespace JetApplication;


use Jet\Form;
use Jet\UI_tabs;

interface Admin_Managers_Entity_Edit_Common
{
	public function renderToolbar(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?Form                                                   $form = null,
		?callable                                               $toolbar_renderer=null
	) : string;
	
	
	public function renderEditMain(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		?UI_tabs                                                $tabs=null,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?callable                                               $common_data_fields_renderer=null,
		?callable                                               $toolbar_renderer=null
	) : string;
	
	public function renderEditImages(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		?UI_tabs                                                $tabs=null,
		?Admin_Managers_Entity_Listing                          $listing=null,
		?callable                                               $toolbar_renderer=null
	) : string;
	
	public function renderAdd(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		?UI_tabs $tabs=null,
		?callable $common_data_fields_renderer=null,
	) : string;
	
	public function renderDeleteConfirm(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		string $message
	) : string;
	
	public function renderDeleteNotPossible(
		Entity_Basic|Admin_Entity_Common_Interface $item,
		string $message,
		?callable $reason_renderer=null
	): string;
}