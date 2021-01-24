<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Form;


#[DataModel_Definition(
	name: 'products_parametrization_values',
	database_table_name: 'products_parametrization_values',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Product::class
)]
abstract class Core_Product_ParametrizationValue extends DataModel_Related_1toN
{
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $product_id = 0;

	protected Product|null $product = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $property_id = 0;

	protected Parametrization_Property|null $property = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $value = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $information_is_not_available = false;


	/**
	 * @param Product $product
	 */
	public function setParents( Product $product )
	{
		$this->product = $product;
		$this->product_id = $product->getId();
	}

	/**
	 * @param Parametrization_Property $property
	 */
	public function  setProperty( Parametrization_Property $property )
	{
		$this->property = $property;
		$this->property_id = $property->getId();
	}

	public function getArrayKeyValue() : int
	{
		return $this->property_id;
	}

	public function getProductId() : int
	{
		return $this->product_id;
	}

	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}

	public function getPropertyId() : int
	{
		return $this->property_id;
	}

	public function setPropertyId( int $property_id ) : void
	{
		$this->property_id = $property_id;
	}

	public function getRawValue() : string
	{
		return $this->value;
	}

	public function setRawValue( string $value ) : void
	{
		$this->value = $value;
	}

	public function isInformationIsNotAvailable() : bool
	{
		return $this->information_is_not_available;
	}

	public function setInformationIsNotAvailable( bool $information_is_not_available ) : void
	{
		$this->information_is_not_available = $information_is_not_available;
	}

	public function getValueEditForm() : Form
	{
		/** @noinspection PhpParamsInspection */
		return $this->property->getValueInstance()->getValueEditForm( $this );
	}



}