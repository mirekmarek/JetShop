<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel_PropertyFilter;

use JetApplication\Entity_WithIDAndShopData;
use JetApplication\Property;
use JetApplication\Property_ShopData;
use JetApplication\Property_Filter;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Property_Value;
use JetApplication\ProductListing;

#[DataModel_Definition(
	name: 'property',
	database_table_name: 'properties',
)]
abstract class Core_Property extends Entity_WithIDAndShopData {
	
	public const PROPERTY_TYPE_NUMBER = 'Number';
	public const PROPERTY_TYPE_BOOL = 'Bool';
	public const PROPERTY_TYPE_OPTIONS = 'Options';


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

	
	/**
	 * @var Property_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Property_ShopData::class
	)]
	protected array $shop_data = [];

	
	protected ?Property_Filter $filter = null;
	
	
	public static function initByData( array $this_data, array $related_data = [], DataModel_PropertyFilter $load_filter = null ): static
	{
		
		if(!str_ends_with(static::class, '\Property')) {
			return parent::initByData( $this_data, $related_data, $load_filter );
		}
		
		/**
		 * @var DataModel $class_name
		 */
		$class_name = static::class.'_'.$this_data['type'];
		
		/**
		 * @var Property $item
		 */
		$item = $class_name::initByData( $this_data, $related_data, $load_filter );
		
		return $item;
	}
	


	public function getType() : string
	{
		return $this->type;
	}
	
	public function getDecimalPlaces() : int
	{
		return $this->decimal_places;
	}

	public function setDecimalPlaces( int $decimal_places ) : void
	{
		$this->decimal_places = $decimal_places;
	}
	

	public function getShopData( ?Shops_Shop $shop=null ) : Property_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	
	abstract public function getValueInstance() : Property_Value|null;
	
	
	abstract public function initFilter( ProductListing $listing ) : void;
	
	abstract public function filter() : ?Property_Filter;
	
	
}