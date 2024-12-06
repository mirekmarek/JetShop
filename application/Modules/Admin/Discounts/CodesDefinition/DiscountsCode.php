<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Discounts_Code;
use JetApplication\EShops;

class DiscountsCode extends Discounts_Code implements Admin_Entity_Marketing_Interface
{
	use Admin_Entity_Marketing_Trait;

	
	public function getEditURL(): string
	{
		return Main::getEditUrl( $this->id );
	}
	
	protected function setupAddForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}
	
	public function hasImages(): bool
	{
		return false;
	}
	
	
	protected function setupForm( Form $form ) : void
	{
		$eshop = new Form_Field_Select('eshop', 'e-shop');
		$eshop->setSelectOptions( EShops::getScope() );
		$eshop->setDefaultValue( $this->getEshop()->getKey() );
		$eshop->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$eshop->setFieldValueCatcher( function( string $eshop_key ) {
			$eshop = EShops::get( $eshop_key );
			$this->setEshop( $eshop );
		} );
		
		$form->addField( $eshop );
		
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

}