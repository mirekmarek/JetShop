<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use JetApplication\Pricelists;
use JetApplication\Product_Price;


trait Product_SetPrice
{
	protected ?Form $set_price_form = null;
	
	
	public function getSetPriceForm() : ?Form
	{
		if(
			$this->isVariantMaster() ||
			$this->isSet() ||
			!Main::getCurrentUserCanSetPrice()
		) {
			return null;
		}
		
		if(!$this->set_price_form) {
			$this->set_price_form = new Form('set_price_form', []);
			
			
				foreach(Pricelists::getList() as $pl ) {
					
					$pp = Product_Price::get( $pl, $this->getId() );
					
					$field_name_prefix = '/'.$pl->getCode().'/';

					$vat_rate = new Form_Field_Select( $field_name_prefix.'vat_rate', 'VAT rate:' );
					$vat_rate->setDefaultValue( $pp->getVatRate() );
					$vat_rate->setErrorMessages([
						Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
					]);
					$vat_rate->setFieldValueCatcher(function( $value ) use ($pp) {
						$pp->setVatRate( $value );
						$pp->save();
					});
					$vat_rate->setSelectOptions( $pl->getVatRatesScope() );
					$this->set_price_form->addField( $vat_rate );
					
					
					$price = new Form_Field_Float($field_name_prefix.'price', 'Price:');
					$price->setDefaultValue( $pp->getPrice() );
					$price->setFieldValueCatcher(function( $value ) use ($pp) {
						$pp->setPrice( $value );
						$pp->save();
					});
					$this->set_price_form->addField( $price );
					
				}
			
		}
		
		return $this->set_price_form;
	}
	
}