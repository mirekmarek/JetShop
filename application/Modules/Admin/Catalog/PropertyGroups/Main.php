<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\PropertyGroups;

use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Entity_WithShopData_Manager_Trait;
use JetApplication\Admin_Managers_PropertyGroup;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_PropertyGroup
{
	use Admin_Entity_WithShopData_Manager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'property-group';

	public const ACTION_GET = 'get_property_group';
	public const ACTION_ADD = 'add_property_group';
	public const ACTION_UPDATE = 'update_property_group';
	public const ACTION_DELETE = 'delete_property_group';
	
	
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
			object_class: PropertyGroup::getEntityType(),
			object_type_filter: null,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getInternalName(),
			selected_entity_edit_URL: $selected?->getEditURL()
		);
		
	}
	
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new PropertyGroup();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'property group';
	}
	
}