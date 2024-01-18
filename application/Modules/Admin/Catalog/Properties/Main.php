<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Property;
use JetApplication\Admin_Entity_WithShopData_Manager_Trait;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Property
{
	use Admin_Entity_WithShopData_Manager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'properties';

	public const ACTION_GET = 'get_property';
	public const ACTION_ADD = 'add_property';
	public const ACTION_UPDATE = 'update_property';
	public const ACTION_DELETE = 'delete_property';
	
	public function renderSelectWidget( string $on_select,
	                                                   int $selected_property_id=0,
	                                                   ?string $only_type_filter=null,
	                                                   ?bool $only_active_filter=null,
	                                                   string $name='select_property' ) : string
	{
		
		$selected = $selected_property_id ? Property::get($selected_property_id) : null;
		
		return Admin_Managers::UI()->renderSelectEntityWidget(
			name: $name,
			caption: Tr::_('... select property ...', dictionary: $this->module_manifest->getName()),
			on_select: $on_select,
			object_class: Property::getEntityType(),
			object_type_filter: $only_type_filter,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getInternalName(),
			selected_entity_edit_URL: $selected?->getEditURL()
		);
		
	}

	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new Property();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'property';
	}
	
	public function showType( string $type ) : string
	{
		$types = Property::getTypesScope();
		if(!isset($types[$type])) {
			return '';
		}
		
		return $types[$type];
	}
	
	public function renderProductPropertyEditFormField(
		Form $form,
		int $property_id,
		string $form_field_name_prefix=''
	) : string
	{
		$property = Property::get( $property_id );
		if(!$property) {
			return '';
		}
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('property', $property);
		$view->setVar('form', $form);
		$view->setVar('prefix', $form_field_name_prefix.'/'.$property_id.'/');
		
		return $view->render('product-property-edit-form-field/'.$property->getType());
	}
	
}