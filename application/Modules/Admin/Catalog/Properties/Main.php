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
use Jet\Tr;
use Jet\Translator;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Property;
use JetApplication\Admin_Managers_Trait;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Property
{
	use Admin_Managers_Trait;
	
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
	
	public function showName( int $id ): string
	{
		$res = '';
		
		Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use (&$res, $id) {
				$property = Property::get($id);
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('id', $id);
				
				if($property) {
					$view->setVar('property', $property);
					$res = $view->render('show-name/known');
				} else {
					$res = $view->render('show-name/unknown');
				}
			}
		);
		
		return $res;
	}
	
}