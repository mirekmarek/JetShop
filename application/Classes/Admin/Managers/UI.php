<?php
namespace JetApplication;

use Jet\Form;

interface Admin_Managers_UI
{
	public function initBreadcrumb() : void;
	
	public function renderShopDataBlocks( ?Form $form=null, ?array $shops=null, bool $inline_mode=false, ?callable $renderer=null ) : void;
	
	public function renderSelectEntityWidget(
		string $name,
		string $caption,
		string $on_select,
		string $object_class,
		string|array|null $object_type_filter,
		?bool $object_is_active_filter,
		?string $selected_entity_title,
		?string $selected_entity_edit_URL
	) : string;
	
	
	public function renderEntityActivation(
		Entity_WithIDAndShopData|Entity_WithCodeAndShopData $entity,
		bool $editable
	) : string;
	
	public function renderEntityShopDataActivation(
		Entity_WithIDAndShopData|Entity_WithCodeAndShopData $entity,
		Shops_Shop $shop,
		bool $editable
	) : string;
	
}