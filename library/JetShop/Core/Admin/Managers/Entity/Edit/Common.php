<?php
namespace JetShop;

use Jet\Form;
use Jet\UI_tabs;
use Closure;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Entity_Basic;
use JetApplication\Entity_Common;


interface Core_Admin_Managers_Entity_Edit_Common extends Admin_Managers_Entity_Edit
{
	public function init(
		Entity_Basic|Admin_Entity_Interface $item,
		?Admin_Managers_Entity_Listing      $listing = null,
		?UI_tabs                            $tabs = null
	): void;
	
	
	public function renderToolbar(
		?Form     $form = null,
		?callable $toolbar_renderer = null
	): string;
	
	public function renderEditMain(
		?callable $common_data_fields_renderer = null,
		?callable $toolbar_renderer = null
	): string;
	
	public function renderEditImages(
		?callable $toolbar_renderer = null
	): string;
	
	public function renderAdd(
		?callable $common_data_fields_renderer = null,
		?callable $eshop_data_fields_renderer = null
	): string;
	
	public function renderDeleteConfirm(
		string $message
	): string;
	
	public function renderDeleteNotPossible(
		string    $message,
		?callable $reason_renderer = null
	): string;
	
	public function renderShowName( int $id, null|Entity_Common|Admin_Entity_Interface $item ): string;
	
	public function renderEntityActivation(
		Entity_Common|Admin_Entity_Common_Interface $entity,
		bool $editable,
		?Closure $deactivate_url_creator = null,
		?Closure $activate_url_creator = null
	) : string;
	
	public function renderEntityFormCommonFields( Form $form ) : string;
	
}