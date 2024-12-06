<?php
namespace JetShop;

use Jet\Attributes;
use Jet\DataModel_Definition;
use Jet\DataModel_Definition_Model;
use Jet\DataModel_Definition_Property_DataModel;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\JetShopEntity_Definition;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\Application_Admin;
use JetApplication\Admin_Managers;
use JetApplication\EShop;
use JetApplication\EShops;
use JetApplication\Entity_WithEShopData_EShopData;
use ReflectionClass;

trait Core_Admin_Entity_WithEShopData_Trait {
	use Admin_Entity_Common_Trait;
	
	protected ?Form $_description_edit_form = null;
	
	public function handleImages() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->defineImages();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( $this->isEditable() );
		$manager->handleSelectImageWidgets();
	}
	
	
	protected function defineImage( string $image_class, string $image_title ) : void
	{
		$manager = Admin_Managers::Image();
		
		foreach( EShops::getList() as $eshop) {
			$manager->defineImage(
				entity: static::getEntityType(),
				object_id: $this->id,
				image_class: $image_class,
				image_title: $image_title,
				image_property_getter: function() use ( $eshop, $image_class ): string {
					return $this->getEshopData( $eshop )->getImage( $image_class );
				},
				image_property_setter: function( string $val ) use ( $eshop, $image_class ): void {
					$this->getEshopData( $eshop )->setImage( $image_class, $val );
					$this->getEshopData( $eshop )->save();
				},
				eshop: $eshop
			);
		}
	}
	
	public function uploadImage(
		string $image_class,
		string $tmp_file_path,
		string $file_name,
		EShop $eshop
	) : void {
		$manager = Admin_Managers::Image();
		
		$manager->uploadImage(
			$tmp_file_path,
			$file_name,
			static::getEntityType(),
			$this->id,
			$image_class,
			$eshop
		);
	}
	
	public static function getEntityShopDataInstance() : Entity_WithEShopData_EShopData
	{
		/**
		 * @var DataModel_Definition_Model $def
		 */
		$def = static::getDataModelDefinition();
		
		/**
		 * @var DataModel_Definition_Property_DataModel $prop
		 */
		$prop = $def->getProperty('eshop_data');
		
		$class = $prop->getValueDataModelClass();
		
		return new $class();
	}
	
	public function getDescriptionMode() : bool
	{
		return false;
	}
	
	
	public function getDescriptionEditFormFieldMap() : array
	{
		/**
		 * @var DataModel_Definition_Property_DataModel $dm_definition
		 */
		$dm_definition = DataModel_Definition::get( static::class )->getProperty('eshop_data');
		$class = $dm_definition->getValueDataModelClass();
		
		$reflection = new ReflectionClass( $class );
		
		$definitions = Attributes::getClassPropertyDefinition( $reflection, JetShopEntity_Definition::class );
		
		$map = [];
		foreach( $definitions as $property_name => $definition ) {
			if(!empty($definition['is_description'])) {
				$setter = $definition['setter']??$this->objectSetterMethodName( $property_name );
				
				$map[$property_name] = function( Entity_WithEShopData_EShopData $sd, mixed $value ) use ($setter) {
					$sd->{$setter}($value);
				};
			}
		}
		
		return $map;
	}
	
	public function getDescriptionEditForm() : Form
	{
		if(!$this->_description_edit_form) {
			$fields = [];
			
			$map = $this->getDescriptionEditFormFieldMap();
			
			foreach( EShops::getAvailableLocales() as $locale ) {
				
				$locale_str = $locale->toString();
				
				foreach(EShops::getListSorted() as $eshop ) {
					if($eshop->getLocale()->toString()!=$locale_str) {
						continue;
					}
					
					$ed = $this->getEshopData( $eshop );
					$edit_form = $ed->createForm('');
					
					foreach($map as $name=>$setter) {
						$field = $edit_form->getField( $name );
						$field->setName( '/description/'.$locale_str.'/'.$name );
						$field->setFieldValueCatcher( function( string $value ) use ($locale_str, $setter) {
							foreach($this->eshop_data as $sd) {
								if($sd->getLocale()->toString()==$locale_str) {
									$setter( $sd, $value );
								}
							}
						} );
						
						$fields[] = $field;
					}
					
					continue 2;
				}
			}
			
			$this->_description_edit_form = new Form('description_edit_form', $fields);
		}
		
		return $this->_description_edit_form;
	}
	
	public function catchDescriptionEditForm() : bool
	{
		if(!$this->getDescriptionEditForm()->catch()) {
			return false;
		}
		
		$this->save();
		return true;
	}
	
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			
			$internal_code = $this->_add_form->getField('internal_code');
			
			$internal_code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if($value==='') {
					return true;
				}
				
				if(static::internalCodeUsed($value)) {
					$field->setError('code_used');
					
					return false;
				}
				
				return true;
			});
			
			
			if($this->getDescriptionMode()) {
				$description_edit_form = $this->getDescriptionEditForm();
				foreach($description_edit_form->getFields() as $f) {
					$this->_add_form->addField( $f );
				}
			}
			
			
			$this->setupAddForm( $this->_add_form );
			
		}
		
		return $this->_add_form;
	}
	
	public function getEditForm() : Form
	{
		
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			
			
			$internal_code = $this->_edit_form->getField('internal_code');
			
			$internal_code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if($value==='') {
					return true;
				}
				
				if(static::internalCodeUsed($value, $this->getId())) {
					$field->setError('code_used');
					
					return false;
				}
				
				return true;
			});
			
			if($this->getDescriptionMode()) {
				$description_edit_form = $this->getDescriptionEditForm();
				foreach($description_edit_form->getFields() as $f) {
					$this->_edit_form->addField( $f );
				}
			}
			
			if(!$this->isEditable()) {
				$this->_edit_form->setIsReadonly();
			}
			
			
			$this->setupEditForm( $this->_edit_form );
		}
		
		return $this->_edit_form;
	}
	
	
}
