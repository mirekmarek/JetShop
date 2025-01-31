<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use Jet\DataModel_Definition_Property_DataModel;
use Jet\Form;
use Jet\Form_Field_Input;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShops;
use JetApplication\EShopEntity_WithEShopData_EShopData;

trait Core_EShopEntity_Admin_WithEShopData_Trait {
	use EShopEntity_Admin_Trait;
	
	protected ?Form $_description_edit_form = null;
	
	
	public function getDescriptionMode() : bool
	{
		return (bool)EShopEntity_Definition::get( $this )->getDescriptionMode();
	}
	
	public function getSeparateTabFormShopData() : bool
	{
		return (bool)EShopEntity_Definition::get( $this )->getSeparateTabFormShopData();
	}
	
	
	public function getDescriptionEditFormFieldMap() : array
	{
		/**
		 * @var DataModel_Definition_Property_DataModel $dm_definition
		 */
		$dm_definition = DataModel_Definition::get( static::class )->getProperty('eshop_data');
		$class = $dm_definition->getValueDataModelClass();
		
		$en_definition = EShopEntity_Definition::get( $class );
		
		$map = [];
		if($en_definition) {
			foreach($en_definition->getProperties() as $p_name=>$p_def) {
				if($p_def->isDescription()) {
					$setter = $p_def->getSetter()??$this->objectSetterMethodName( $p_name );
					
					$map[$p_name] = function( EShopEntity_WithEShopData_EShopData $sd, mixed $value ) use ($setter) {
						$sd->{$setter}($value);
					};
					
				}
			}
		}
		
		
		return $map;
	}
	
	public function getDescriptionEditForm() : Form
	{
		if(!$this->_description_edit_form) {
			$fields = [];
			$map = $this->getDescriptionEditFormFieldMap();
			
			
			if(!$this->getDescriptionMode()) {
				foreach($this->getEditForm()->getFields() as $field) {
					$name = explode('/', $field->getName());
					if(
						isset($name[3]) &&
						$name[1]=='eshop_data' &&
						isset($map[$name[3]])
					) {
						$fields[$field->getName()] = $field;
					}
				}
			} else {
				
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
								
								foreach(EShops::getList() as $eshop) {
									$sd = $this->getEshopData($eshop);
									
									if($sd->getLocale()->toString()==$locale_str) {
										$setter( $sd, $value );
									}
								}
								
							} );
							
							$fields[$field->getName()] = $field;
						}
						
						continue 2;
					}
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
	
	public function getEditMainForm() : Form
	{
		
		if( $this->getSeparateTabFormShopData() ) {
			if(!$this->_edit_main_form) {
				$map = $this->getDescriptionEditFormFieldMap();
				
				$this->_edit_main_form = $this->getEditForm();
				
				foreach($this->_edit_main_form->getFields() as $field) {
					$name = explode('/', $field->getName());
					if(
						isset($name[3]) &&
						$name[1]=='eshop_data' &&
						isset($map[$name[3]])
					) {
						$this->_edit_main_form->removeField( $field->getName() );
					}
				}
			}
			
			return $this->_edit_main_form;
			
		} else {
			return $this->getEditForm();
		}
	}
	
	public function catchEditMainForm() : bool
	{
		if( $this->getSeparateTabFormShopData() ) {
			return $this->getEditMainForm()->catch();
		} else {
			return $this->catchEditForm();
		}
		
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
