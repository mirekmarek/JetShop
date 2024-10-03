<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;

use JetApplication\Admin_Managers;
use JetApplication\Entity_WithShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\Product_Parameter;
use JetApplication\ProductFilter;
use JetApplication\Property;
use JetApplication\Property_Options_Option;
use JetApplication\Property_ShopData;
use JetApplication\Property_Type;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\KindOfProduct_Property;

#[DataModel_Definition(
	name: 'property',
	database_table_name: 'properties',
)]
abstract class Core_Property extends Entity_WithShopData implements FulltextSearch_IndexDataProvider
{
	
	public const PROPERTY_TYPE_NUMBER  = 'Number';
	public const PROPERTY_TYPE_BOOL    = 'Bool';
	public const PROPERTY_TYPE_OPTIONS = 'Options';
	public const PROPERTY_TYPE_TEXT    = 'Text';

	protected ?Property_Type $_type_instance = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $type = '';

	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Decimal places:',
	)]
	protected int $decimal_places = 0;
	
	
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
	
	
	/**
	 * @var Property_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Property_ShopData::class
	)]
	protected array $shop_data = [];
	
	/**
	 * @var Property_Options_Option[]
	 */
	protected ?array $options = null;
	
	
	public function getType() : string
	{
		return $this->type;
	}
	
	public function setType( string $type ) : void
	{
		$this->type = $type;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setType( $type );
		}
	}
	
	public function getDecimalPlaces() : int
	{
		return $this->decimal_places;
	}
	
	public static function getTypes( array $ids ) : array
	{
		if(!$ids) {
			return [];
		}
		
		return  static::dataFetchPairs(
			select:['id', 'type'],
			where: ['id'=>$ids],
			raw_mode: true
		);
	}

	public function setDecimalPlaces( int $decimal_places ) : void
	{
		$this->decimal_places = $decimal_places;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setDecimalPlaces( $decimal_places );
		}
	}
	

	public function getShopData( ?Shops_Shop $shop=null ) : Property_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
	}
	
	
	/***
	 * @param array $ids
	 * @return static[]
	 */
	public static function getProperties( array $ids ) : array
	{
		return Property::fetch( ['property'=>['id'=>$ids]] );
	}
	
	
	public function getTypeInstance() : Property_Type
	{
		if(!$this->_type_instance) {
			$class_name = Property_Type::class.'_'.$this->type;
			
			$this->_type_instance = new $class_name( $this );
		}
		
		return $this->_type_instance;
	}
	
	
	public function getProductParameter(): ?Product_Parameter
	{
		return $this->getTypeInstance()->getProductParameterValue();
	}
	
	
	public function assocToProduct( int $product_id ) : void
	{
		$this->setProductParameter(
			new Product_Parameter(
				$product_id,
				$this->id
			)
		);
	}
	
	public function setProductParameter( Product_Parameter $product_parameter ): void
	{
		$this->getTypeInstance()->setProductParameter( $product_parameter );
	}
	
	public function getProductParameterValue() : mixed
	{
		return $this->getTypeInstance()->getProductParameterValue();
	}
	
	public function canBeVariantSelector() : bool
	{
		return $this->getTypeInstance()->canBeVariantSelector();
	}
	
	public function canBeFilter() : bool
	{
		return $this->getTypeInstance()->canBeFilter();
	}
	
	public function getIsFilter(): bool
	{
		if(!$this->canBeFilter()) {
			return false;
		}
		return $this->is_filter;
	}
	
	public function setIsFilter( bool $is_filter ): void
	{
		if(!$this->canBeFilter()) {
			$is_filter = false;
		}
		
		$this->is_filter = $is_filter;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setIsFilter( $is_filter );
		}
	}
	
	public function getFilterPriority(): int
	{
		return $this->filter_priority;
	}
	
	public function setFilterPriority( int $filter_priority ): void
	{
		$this->filter_priority = $filter_priority;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setFilterPriority( $filter_priority );
		}
	}
	
	public function getIsDefaultFilter(): bool
	{
		if(!$this->canBeFilter()) {
			return false;
		}
		
		return $this->is_default_filter;
	}
	
	public function setIsDefaultFilter( bool $is_default_filter ): void
	{
		if(!$this->canBeFilter()) {
			$is_default_filter = false;
		}
		
		$this->is_default_filter = $is_default_filter;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setIsDefaultFilter( $is_default_filter );
		}
	}
	
	
	
	public function getValueEditForm() : Form
	{
		return $this->getTypeInstance()->getValueEditForm();
	}
	
	public function getProductFilterEditForm( ProductFilter $filter ) : ? Form
	{
		return $this->getTypeInstance()->getProductFilterEditForm( $filter );
	}
	
	
	
	/**
	 * @return Property_Options_Option[]
	 */
	public function getOptions() : array
	{
		if($this->options===null) {
			$this->options = Property_Options_Option::getListForProperty( $this->id );
		}
		
		return $this->options;
	}
	
	
	public function addOption( Property_Options_Option $option ) : void
	{
		$this->getOptions();
		
		$option->setPriority( count($this->options)+1 );
		$option->setPropertyId( $this->id );
		$option->save();
		$this->options[$option->getId()] = $option;
		
		$option->activate();
		foreach(Shops::getList() as $shop) {
			if(!$option->getShopData($shop)->isActiveForShop()) {
				$option->getShopData($shop)->_activate();
			}
		}
	}
	
	
	public function getOption( int $id ) : Property_Options_Option|null
	{
		$this->getOptions();
		
		if(!isset($this->options[$id])) {
			return null;
		}
		
		return $this->options[$id];
	}
	
	
	public function sortOptions( array $sort ) : void
	{
		$this->getOptions();
		$i = 0;
		foreach($sort as $id) {
			if(isset($this->options[$id])) {
				$i++;
				$this->options[$id]->setPriority($i);
				$this->options[$id]->save();
			}
		}
	}
	
	public function getFulltextObjectType(): string
	{
		return '';
	}
	
	public function getFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getInternalFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getInternalFulltextTexts(): array
	{
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
	public function getShopFulltextTexts( Shops_Shop $shop ) : array
	{
		return [];
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
	public static function getFilterablePropertyIds() : array
	{
		$property_ids = static::dataFetchCol(
			select: ['id'],
			where: [
				'is_filter' => true
			]
		);
		
		$property_ids = array_unique( $property_ids );
		
		return $property_ids;
	}
	
	public function getUsageKindOfProductIds() : array
	{
		return KindOfProduct_Property::dataFetchCol(
			select: [
				'kind_of_product_id'
			],
			where: [
				'property_id' => $this->getId()
			]
		);
		
	}
	
}