<?php
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Input;
use JetApplication\Admin_Entity_Trait;
use JetApplication\Application_Admin;
use JetApplication\Admin_Managers;

trait Core_Admin_Entity_Common_Trait {
	
	
	use Admin_Entity_Trait;
	
	public function handleImages() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->defineImages();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( $this->isEditable() );
		$manager->handleSelectImageWidgets();
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
			
			if(!$this->isEditable()) {
				$this->_edit_form->setIsReadonly();
			}
			
			
			$this->setupEditForm( $this->_edit_form );
		}
		
		return $this->_edit_form;
	}
}