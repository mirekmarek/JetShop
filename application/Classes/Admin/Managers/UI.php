<?php
namespace JetApplication;

use Jet\Form;

use Closure;

interface Admin_Managers_UI
{
	public function handleCurrentPreferredShop();
	
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
		Entity_Basic $entity,
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
	

	public function renderEntityToolbar( Form $form, ?callable $buttons_renderer=null ) : string;
	
}