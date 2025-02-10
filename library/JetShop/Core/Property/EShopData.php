<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Product_Parameter;
use JetApplication\Property;
use JetApplication\Property_Type;

#[DataModel_Definition(
	name: 'property_eshop_data',
	database_table_name: 'properties_eshop_data',
	parent_model_class: Property::class
)]
abstract class Core_Property_EShopData extends EShopEntity_WithEShopData_EShopData implements EShopEntity_HasImages_Interface
{
	use EShopEntity_HasImages_Trait;
	
	protected ?Property_Type $_type_instance = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $type = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $decimal_places = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description:',
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $image_main = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $image_pictogram = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Description for YES:',
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $bool_yes_description='';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $url_param = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Units (mm, cm, ...):',
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $units = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is filter',
	)]
	protected bool $is_filter = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Filter priority',
	)]
	protected int $filter_priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is default filter',
	)]
	protected bool $is_default_filter = false;
	
	public function getType(): string
	{
		return $this->type;
	}
	
	public function setType( string $type ): void
	{
		$this->type = $type;
	}
	
	public function getDecimalPlaces(): int
	{
		return $this->decimal_places;
	}
	
	public function setDecimalPlaces( int $decimal_places ): void
	{
		$this->decimal_places = $decimal_places;
	}

	public function setLabel( string $label ) : void
	{
		if($this->label==$label) {
			return;
		}
		$this->label = $label;
	}

	public function getLabel() : string
	{
		return $this->label;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function getBoolYesDescription() : string
	{
		return $this->bool_yes_description;
	}

	public function setBoolYesDescription( string $bool_yes_description ) : void
	{
		$this->bool_yes_description = $bool_yes_description;
	}

	public function getUrlParam() : string
	{
		return $this->url_param;
	}

	public function setUrlParam( string $url_param ) : void
	{
		$this->url_param = $url_param;
	}

	public function getUnits() : string
	{
		return $this->units;
	}

	public function setUnits( string $units ) : void
	{
		$this->units = $units;
	}
	
	public function getImageMain(): string
	{
		return $this->image_main;
	}
	
	public function setImageMain( string $image_main ): void
	{
		$this->image_main = $image_main;
	}
	
	public function getImagePictogram(): string
	{
		return $this->image_pictogram;
	}
	
	public function setImagePictogram( string $image_pictogram ): void
	{
		$this->image_pictogram = $image_pictogram;
	}
	
	
	public function getTypeInstance() : Property_Type
	{
		if(!$this->_type_instance) {
			$class_name = Property_Type::class.'_'.$this->type;
			
			$this->_type_instance = new $class_name( $this );
		}
		
		return $this->_type_instance;
	}
	
	public function assocToProduct( int $product_id ) : void
	{
		$this->setProductParameter(
			new Product_Parameter(
				$product_id,
				$this->getEntityId()
			)
		);
	}
	
	public function getProductParameter(): ?Product_Parameter
	{
		return $this->getTypeInstance()->getProductParameterValue();
	}
	
	public function setProductParameter( Product_Parameter $product_parameter ): void
	{
		$this->getTypeInstance()->setProductParameter( $product_parameter );
	}
	
	public function getProductParameterValue() : mixed
	{
		return $this->getTypeInstance()->getProductParameterValue();
	}
	
	public function getProductDetailDisplayValue() : mixed
	{
		return $this->getTypeInstance()->getProductDetailDisplayValue( $this->getEshop() );
	}
	
	public function getIsFilter(): bool
	{
		return $this->is_filter;
	}
	
	public function setIsFilter( bool $is_filter ): void
	{
		$this->is_filter = $is_filter;
	}
	
	public function getFilterPriority(): int
	{
		return $this->filter_priority;
	}
	
	public function setFilterPriority( int $filter_priority ): void
	{
		$this->filter_priority = $filter_priority;
	}
	
	public function getIsDefaultFilter(): bool
	{
		return $this->is_default_filter;
	}
	
	public function setIsDefaultFilter( bool $is_default_filter ): void
	{
		$this->is_default_filter = $is_default_filter;
	}
	
}