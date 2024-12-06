<?php
namespace JetShop;

use Jet\Form;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Entity_Marketing;

interface Core_Admin_Managers_Entity_Edit_Marketing extends Admin_Managers_Entity_Edit
{
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
	
	public function renderShowName( int $id, Entity_Marketing|Admin_Entity_Marketing_Interface $entity ): string;
	
	public function renderEditProducts( Entity_Marketing|Admin_Entity_Marketing_Interface $item ): string;
	
	public function renderEditFilter( Entity_Marketing|Admin_Entity_Marketing_Interface $item, Form $form ): string;
	
}