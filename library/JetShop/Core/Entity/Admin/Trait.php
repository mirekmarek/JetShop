<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Form;
use Jet\Form_Field_Input;
use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Entity_HasEShopRelation_Interface;
use JetApplication\Entity_HasInternalParams_Interface;
use JetApplication\EShops;
use JetApplication\Entity_Definition;

trait Core_Entity_Admin_Trait {
	
	
	protected ?Form $_add_form = null;
	
	protected ?Form $_edit_form = null;
	
	protected bool $editable;
	
	public function getAdminManager() : null|Application_Module|Admin_EntityManager_Interface
	{
		$ifc = $this->getAdminManagerInterface();
		if(!$ifc) {
			return null;
		}
		
		return Admin_Managers::get( $ifc );
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
	
	
	
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			if($this instanceof Entity_HasEShopRelation_Interface) {
				$this->setEshop( EShops::getCurrent() );
			}
			
			$this->_add_form = $this->createForm('add_form');
			
			$this->setupAddForm( $this->_add_form );
			
			if(
				$this instanceof Entity_HasInternalParams_Interface &&
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
				$this instanceof Entity_HasInternalParams_Interface &&
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
		return Entity_Definition::get( $this )?->getAdminManagerInterface();
	}
	
	public function renderActiveState() : string
	{
		return $this->getAdminManager()->renderActiveState( $this );
	}
	
	
}