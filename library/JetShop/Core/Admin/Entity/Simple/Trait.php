<?php
namespace JetShop;

use Jet\Form;
use JetApplication\Admin_Entity_Trait;

trait Core_Admin_Entity_Simple_Trait {
	
	
	use Admin_Entity_Trait;
	
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			$this->setupAddForm( $this->_add_form );
			
		}
		
		return $this->_add_form;
	}
	
	public function getEditForm() : Form
	{

		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			
			if(!$this->isEditable()) {
				$this->_edit_form->setIsReadonly();
			}
			
			$this->setupEditForm( $this->_edit_form );
		}
		
		return $this->_edit_form;
	}
}