<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Properties;


use Jet\Factory_MVC;
use Jet\Form;
use JetApplication\Application_Service_Admin_Property;
use JetApplication\EShopEntity_Basic;
use JetApplication\Property;


class Main extends Application_Service_Admin_Property
{
	public const ADMIN_MAIN_PAGE = 'properties';
	
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