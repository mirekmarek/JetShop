<?php
namespace JetShop;


use Jet\Form;
use Jet\Form_Field_Checkbox;
use JetApplication\Product_Parameter;
use JetApplication\ProductFilter;
use JetApplication\Property;
use JetApplication\Property_EShopData;
use JetApplication\EShop;


abstract class Core_Property_Type
{

	protected Property|Property_EShopData $property_model;
	protected int $property_id;
	
	protected ?Product_Parameter $product_parameter = null;
	
	
	public function __construct( Property|Property_EShopData $property_model )
	{
		$this->property_model = $property_model;
		if($property_model instanceof Property_EShopData) {
			$this->property_id = $property_model->getEntityId();
		} else {
			$this->property_id = $property_model->getId();
		}
	}
	
	public function getProductParameter(): ?Product_Parameter
	{
		return $this->product_parameter;
	}
	
	public function setProductParameter( Product_Parameter $product_parameter ): void
	{
		$this->product_parameter = $product_parameter;
	}
	
	
	abstract public function getProductParameterValue() : mixed;
	
	abstract public function canBeVariantSelector() : bool;
	
	public function canBeFilter() : bool
	{
		return true;
	}
	
	abstract public function setupForm( Form $form ) : void;
	
	
	abstract public function getValueEditForm() : Form;
	
	protected function _getValueEditForm( array $value_fields ) : Form
	{
		$information_in_not_available = new Form_Field_Checkbox(
			'information_in_not_available',
			'Information is not available',
		);
		$information_in_not_available->setDefaultValue( $this->product_parameter->getInfoNotAvl() );
		$information_in_not_available->setFieldValueCatcher( function( $value ) {
			$this->product_parameter->setInfoNotAvl( $value );
		} );
		
		$value_fields[] = $information_in_not_available;
		
		
		return new Form('properties_edit_form', $value_fields);
		
	}
	
	abstract public function getProductFilterEditForm( ProductFilter $filter ) : ? Form;
	
	abstract public function getProductDetailDisplayValue( ?EShop $eshop=null ) : mixed;
}