<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Discounts_Code;
use JetApplication\Shops;

class DiscountsCode extends Discounts_Code implements Admin_Entity_Common_Interface
{
	protected ?Form $_add_form = null;
	
	protected ?Form $_edit_form = null;
	
	protected bool $editable;
	
	public function isEditable(): bool
	{
		return $this->editable;
	}
	
	public function setEditable( bool $editable ): void
	{
		$this->editable = $editable;
	}

	
	public function getAdminFulltextObjectClass(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectId(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectType(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectIsActive(): bool
	{
		return false;
	}
	
	public function getAdminFulltextObjectTitle(): string
	{
		return '';
	}
	
	public function getAdminFulltextTexts(): array
	{
		return [];
	}

	
	
	public function getEditURL(): string
	{
		return Main::getEditUrl( $this->id );
	}
	
	public function getAdminTitle(): string
	{
		return $this->getCode();
	}
	
	public function isItPossibleToDelete(): bool
	{
		return false;
	}
	
	protected function setupForm( Form $form ) : void
	{
		$shop = new Form_Field_Select('shop', 'Shop');
		$shop->setSelectOptions( Shops::getScope() );
		$shop->setDefaultValue( Shops::getCurrent()->getKey() );
		$shop->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$shop->setFieldValueCatcher( function( string $shop_key ) {
			$shop = Shops::get( $shop_key );
			$this->setShop( $shop );
		} );
		
		$form->addField( $shop );
		
		$code = $form->getField('code');
		
		$code->setValidator(function( Form_Field_Input $field ) {
			$value = $field->getValue();
			if($value==='') {
				return true;
			}
			
			if(static::codeExists($value, $this->getId())) {
				$field->setError('code_exists');
				
				return false;
			}
			
			return true;
		});
		
	}
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			$this->setupForm( $this->_add_form );
		}
		
		return $this->_add_form;
	}
	
	
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}
	
	public function getEditForm() : Form
	{
		
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			
			$this->setupForm( $this->_edit_form );
			
			if(!$this->isEditable()) {
				$this->_edit_form->setIsReadonly();
			}
			
		}
		
		return $this->_edit_form;
	}
	
	public function catchEditForm(): bool
	{
		return $this->getEditForm()->catch();
	}
	
	public function isActive() : bool
	{
		if(!$this->valid_from && !$this->valid_till) {
			return true;
		}
		
		$now = Data_DateTime::now();
		if(
			$this->valid_till &&
			$this->valid_till<$now
		) {
			return false;
		}
		
		if(
			$this->valid_from &&
			$this->valid_from>$now
		) {
			return false;
		}

		return true;
	}
	
	public function isExpired() : bool
	{
		if(
			$this->isActive() ||
			!$this->valid_till
		) {
			return false;
		}

		$now = Data_DateTime::now();
		return $now<$this->valid_till;
	}
	
	public function isWaiting() : bool
	{
		if(
			$this->isActive() ||
			!$this->valid_from
		) {
			return false;
		}
		
		$now = Data_DateTime::now();
		
		
		return $this->valid_from>$now;
	}

}