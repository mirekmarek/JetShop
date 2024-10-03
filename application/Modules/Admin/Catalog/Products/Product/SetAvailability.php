<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use Jet\Form_Field_Date;
use Jet\Form_Field_Int;
use JetApplication\Availabilities;
use JetApplication\Product_Availability;


trait Product_SetAvailability
{
	protected ?Form $set_availability_form = null;
	
	
	public function getSetAvailabilityForm() : ?Form
	{
		if(
			$this->isVariantMaster() ||
			$this->isSet() ||
			!Main::getCurrentUserCanSetAvailability()
		) {
			return null;
		}
		
		if(!$this->set_availability_form) {
			$this->set_availability_form = new Form('set_availability_form', []);
			
				foreach(Availabilities::getList() as $availability) {
					$product_avl = Product_Availability::get( $availability, $this->getId() );
					
					$field_name_prefix = '/'.$availability->getCode().'/';
					
					
					$available_from = new Form_Field_Date($field_name_prefix.'available_from', 'Available from:');
					$available_from->setErrorMessages([
						Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid value'
					]);
					$available_from->setDefaultValue( $product_avl->getAvailableFrom() );
					$available_from->setFieldValueCatcher( function( $value ) use ( $product_avl ) {
						$product_avl->setAvailableFrom( $value );
					} );
					$this->set_availability_form->addField( $available_from );
					
					
					$length_of_delivery = new Form_Field_Int($field_name_prefix.'length_of_delivery', 'Length of delivery:');
					$length_of_delivery->setDefaultValue( $product_avl->getLengthOfDelivery() );
					$length_of_delivery->setFieldValueCatcher( function( $value ) use ( $product_avl ) {
						$product_avl->setLengthOfDelivery( $value );
					} );
					$this->set_availability_form->addField( $length_of_delivery );
					
					
				}

		}
		
		return $this->set_availability_form;
	}
	
}