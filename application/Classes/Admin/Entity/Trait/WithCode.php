<?php
namespace JetApplication;


use Jet\Form;
use Jet\Form_Field_Input;

trait Admin_Entity_Trait_WithCode {
	use Admin_Entity_Trait;
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			$code = $this->_add_form->getField('code');
			
			$code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				
				if(static::exists($value)) {
					$field->setError('code_used');
					
					return false;
				}
				
				return true;
			});
			
		}
		
		return $this->_add_form;
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			$this->_edit_form->getField('code')->setIsReadonly(true);
		}
		
		return $this->_edit_form;
	}
	
}