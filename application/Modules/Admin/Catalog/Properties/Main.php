<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Properties;


use Jet\Factory_MVC;
use Jet\Form;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Property;
use JetApplication\EShopEntity_Basic;
use JetApplication\Property;


class Main extends Admin_Managers_Property
{
	public const ADMIN_MAIN_PAGE = 'properties';
	
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
			entity_type: Property::getEntityType(),
			object_type_filter: $only_type_filter,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getInternalName(),
			selected_entity_edit_URL: $selected?->getEditUrl()
		);
		
	}

	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Property();
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