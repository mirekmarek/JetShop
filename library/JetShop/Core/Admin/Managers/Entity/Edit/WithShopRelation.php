<?php
namespace JetShop;

use Jet\Form;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Admin_Entity_WithShopRelation_Interface;

interface Core_Admin_Managers_Entity_Edit_WithShopRelation extends Admin_Managers_Entity_Edit
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
		?callable $shop_data_fields_renderer = null
	): string;
	
	public function renderDeleteConfirm(
		string $message
	): string;
	
	public function renderDeleteNotPossible(
		string    $message,
		?callable $reason_renderer = null
	): string;
	
	public function renderShowName( int $id, Entity_WithShopRelation|Admin_Entity_WithShopRelation_Interface $entity ): string;
	
}