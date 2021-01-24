<?php
namespace JetShop;

use Jet\Form;

abstract class Core_Parametrization_Property_Value {

	protected Parametrization_Property|null $property = null;

	public function __construct( Parametrization_Property $property )
	{
		$this->property = $property;
	}

	public function getProperty() : Parametrization_Property
	{
		return $this->property;
	}

	abstract public function getValueEditForm( Product_ParametrizationValue $product_value ) : Form;

}