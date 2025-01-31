<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;

interface Core_Admin_Managers_UI
{
	public function handleCurrentPreferredShop();
	
	public function renderSelectEntityWidget(
		string $name,
		string $caption,
		string $on_select,
		string $entity_type,
		string|array|null $object_type_filter,
		?bool $object_is_active_filter,
		?string $selected_entity_title,
		?string $selected_entity_edit_URL
	) : string;
	
	
	public function renderEntityToolbar( Form $form, ?callable $buttons_renderer=null ) : string;
	
}