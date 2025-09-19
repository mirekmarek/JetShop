<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Input;
use Jet\Tr;
use JetApplication\Admin_EntityManager_Module;
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasInternalParams_Interface;
use JetApplication\EShops;
use JetApplication\EShopEntity_Definition;

trait Core_EShopEntity_Admin_Trait {
	
	
	protected ?Form $_add_form = null;
	
	protected ?Form $_edit_form = null;
	
	protected bool $editable;
	
	public function getAdminManager() : ?Admin_EntityManager_Module
	{
		$ifc = $this->getAdminManagerInterface();
		if(!$ifc) {
			return null;
		}
		
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Application_Service_Admin::list()->get( $ifc );
	}
	
	
	public function isEditable(): bool
	{
		return $this->editable;
	}
	
	public function setEditable( bool $editable ): void
	{
		$this->editable = $editable;
	}
	
	public function getEditUrl( array $get_params=[] ) : string
	{
		return $this->getAdminManager()->getEditUrl( $this, $get_params );
	}
	
	protected ?Form $_edit_main_form = null;
	
	public function getEditMainForm() : Form
	{
		return $this->getEditForm();
	}
	
	public function catchEditMainForm() : bool
	{
		return $this->catchEditForm();
	}
	
	
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			if($this instanceof EShopEntity_HasEShopRelation_Interface) {
				$this->setEshop( EShops::getCurrent() );
			}
			
			$this->_add_form = $this->createForm('add_form');
			
			$this->setupAddForm( $this->_add_form );
			
			if(
				$this instanceof EShopEntity_HasInternalParams_Interface &&
				$this->_add_form->fieldExists('internal_code')
			) {
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
				
			}
			
			
		}
		
		return $this->_add_form;
	}
	
	protected function setupAddForm( Form $form ) : void
	{
	}
	
	
	public function getEditForm() : Form
	{
		
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			
			if(!$this->isEditable()) {
				$this->_edit_form->setIsReadonly();
			}
			
			$this->setupEditForm( $this->_edit_form );
			
			
			if(
				$this instanceof EShopEntity_HasInternalParams_Interface &&
				$this->_edit_form->fieldExists('internal_code')
			) {
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
				
			}
			
		}
		
		return $this->_edit_form;
	}
	
	protected function setupEditForm( Form $form ) : void
	{
	}
	
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}
	
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}
	
	public function getAdminManagerInterface() : ?string
	{
		return EShopEntity_Definition::get( $this )?->getAdminManagerInterface();
	}
	
	public function renderActiveState() : string
	{
		return $this->getAdminManager()->renderActiveState( $this );
	}
	
	
	public static function hasCommonPropertiesEditableByListingActions() : bool
	{
		$def = EShopEntity_Definition::get( static::class );
		foreach($def->getProperties() as $property) {
			if($property->isEditableByListingAction()) {
				return true;
			}
		}
		
		return false;
	}
	
	public function createListingActionCommonPropertiesEditForm() : Form
	{
		$properties = [];
		$def = EShopEntity_Definition::get( $this );
		foreach($def->getProperties() as $property_name => $property) {
			if($property->isEditableByListingAction()) {
				$properties[] = $property_name;
			}
		}
		
		$form = $this->createForm('listing_action_common_properties_edit_form', $properties);
		foreach($form->getFields() as $field) {
			$field->setIsRequired( false );
		}
		
		foreach( $properties as $property_name ) {
			$set_chb = new Form_Field_Checkbox('/set/'.$property_name, Tr::_('Set value', dictionary: Tr::COMMON_DICTIONARY));
			$form->addField( $set_chb );
		}
		
		return $form;
	}
	
	public function catchListingActionCommonPropertiesEditForm() : bool
	{
		$form = $this->createListingActionCommonPropertiesEditForm();
		if(
			!$form->catchInput() ||
			!$form->validate()
		) {
			return false;
		}
		
		$properties = [];
		$def = EShopEntity_Definition::get( static::class );
		foreach($def->getProperties() as $property_name => $property) {
			if($property->isEditableByListingAction()) {
				$properties[] = $property_name;
			}
		}
		
		foreach( $properties as $property_name ) {
			if(!$form->field('/set/'.$property_name)->getValue()) {
				continue;
			}
			
			$form->field($property_name)->catchFieldValue();
		}
		
		
		
		return true;
	}
	
}