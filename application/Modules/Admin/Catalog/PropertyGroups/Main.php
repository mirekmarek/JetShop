<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\PropertyGroups;


use Jet\Tr;
use JetApplication\EShopEntity_Basic;
use JetApplication\PropertyGroup;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_PropertyGroup;


class Main extends Admin_Managers_PropertyGroup
{
	public const ADMIN_MAIN_PAGE = 'property-group';
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_property_group_id=0,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_property_group' ) : string
	{
		
		$selected = $selected_property_group_id ? PropertyGroup::get($selected_property_group_id) : null;

		return Admin_Managers::UI()->renderSelectEntityWidget(
			name: $name,
			caption: Tr::_('... select property group ...', dictionary: $this->module_manifest->getName()),
			on_select: $on_select,
			entity_type: PropertyGroup::getEntityType(),
			object_type_filter: null,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getInternalName(),
			selected_entity_edit_URL: $selected?->getEditUrl()
		);
		
	}
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new PropertyGroup();
	}
}