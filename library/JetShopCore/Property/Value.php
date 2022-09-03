<?php
namespace JetShop;

use Jet\Form;

abstract class Core_Property_Value {

	protected Property|null $property = null;

	public function __construct( Property $property )
	{
		$this->property = $property;
	}

	public function getProperty() : Property
	{
		return $this->property;
	}

	abstract public function getValueEditForm( Product_Parameter $product_value ) : Form;

}