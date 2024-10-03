<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\Form;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Property as Application_Property;


class Property extends Application_Property implements Admin_Entity_WithShopData_Interface {
	use Admin_Entity_WithShopData_Trait;
	
	
	protected static array $types = [
		self::PROPERTY_TYPE_NUMBER => 'Number',
		self::PROPERTY_TYPE_BOOL => 'Yes / No',
		self::PROPERTY_TYPE_OPTIONS => 'Options',
		self::PROPERTY_TYPE_TEXT => 'Text',
	];
	
	
	public static function getTypesScope() : array
	{
		$list = [];
		
		foreach( self::$types as $type=>$label ) {
			$list[$type] = Tr::_($label);
		}
		
		return $list;
	}
	
	public function getTypeTitle() : string
	{
		return static::getTypesScope()[$this->getType()];
	}
	
	protected function setupAddForm( Form $form ) : void
	{
		$this->getTypeInstance()->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		if(!$this->getTypeInstance()->canBeFilter()) {
			$form->field('is_filter')->setIsReadonly(true);
			$form->field('is_default_filter')->setIsReadonly(true);
			$form->field('filter_priority')->setIsReadonly(true);
		}
		
		$this->getTypeInstance()->setupForm( $form );
	}
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'main',
			image_title:  Tr::_('Main image')
		);
		$this->defineImage(
			image_class:  'pictogram',
			image_title:  Tr::_('Pictogram image')
		);
	}
	
	/**
	 * @return Property_Options_Option[]
	 */
	public function getOptions() : array
	{
		if($this->options===null) {
			$this->options = Property_Options_Option::getListForProperty( $this->id );
		}
		
		return $this->options;
	}
	
}