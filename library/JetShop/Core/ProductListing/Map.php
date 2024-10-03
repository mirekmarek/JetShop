<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use JetApplication\Pricelists_Pricelist;
use JetApplication\Product_Price;
use JetApplication\ProductListing;
use JetApplication\Property;
use JetApplication\Shops_Shop;
use JetApplication\Product_ShopData;
use JetApplication\Product_Parameter_Value;

abstract class Core_ProductListing_Map
{
	protected Shops_Shop $shop;
	protected Pricelists_Pricelist $pricelist;
	protected array $product_ids;
	
	protected array $brand_ids = [];
	protected array $brands_map = [];
	
	protected array $kind_of_product_ids = [];
	
	protected array $property_ids = [];
	protected array $property_option_ids = [];
	protected array $property_types = [];
	protected array $property_options_map = [];
	protected array $property_numbers_map = [];
	protected array $property_bool_map = [];
	
	protected float $min_price = 0.0;
	protected float $max_price = 0.0;
	
	public function __construct( ProductListing $listing, array $product_ids )
	{
		$this->shop = $listing->getShop();
		$this->pricelist = $listing->getPricelist();
		
		$this->product_ids = [];
		if(!$product_ids) {
			return;
		}
		
		
		$where = Product_ShopData::getActiveQueryWhere( $this->shop );
		$where[] = 'AND';
		$where['entity_id'] = $product_ids;
		
		$prices = Product_Price::getPriceMap( $this->pricelist, $product_ids );
		
		$this->min_price = min($prices);
		$this->max_price = max($prices);
		
		
		$data = Product_ShopData::dataFetchAll(
			select: [
				'id' => 'entity_id',
				'brand_id',
				'kind_id'
			],
			where: $where,
			raw_mode: true
		);
		
		
		foreach($data as $d) {
			$id = (int)$d['id'];
			$brand_id = (int)$d['brand_id'];
			$kind_of_product_id = (int)$d['kind_id'];
			
			if(!in_array($brand_id, $this->brand_ids)) {
				$this->brand_ids[] = $brand_id;
				$this->brands_map[$brand_id] = [];
			}
			$this->brands_map[$brand_id][] = $id;
			
			
			if(!in_array($kind_of_product_id, $this->kind_of_product_ids)) {
				$this->kind_of_product_ids[] = $kind_of_product_id;
			}
			
			$this->product_ids[] = $id;
		}
		
		$filterable_property_ids = Property::getFilterablePropertyIds();
		
		
		if($filterable_property_ids) {
			$property_map= Product_Parameter_Value::dataFetchAll(
				select: [
					'property_id',
					'product_id',
					'value'
				],
				where: [
					'product_id' => $this->product_ids,
					'AND',
					'property_id' => $filterable_property_ids
				],
				raw_mode: true);
		} else {
			$property_map = [];
		}
		
		$property_ids = [];
		foreach($property_map as $d) {
			$property_id = $d['property_id'];
			if(!in_array($property_id, $property_ids)) {
				$property_ids[] = $property_id;
			}
		}
		
		$this->property_types = Property::getTypes( $property_ids );
		
		$this->property_options_map = [];
		
		foreach($property_map as $d) {
			$property_id = (int)$d['property_id'];
			$product_id = (int)$d['product_id'];
			
			switch( $this->property_types[$property_id] ) {
				case Property::PROPERTY_TYPE_OPTIONS:
					$option_id = (int)$d['value'];
					
					if(!isset($this->property_options_map[$property_id])) {
						$this->property_ids[] =$property_id;
						$this->property_options_map[$property_id] = [];
					}
					if(!isset($this->property_options_map[$property_id][$option_id])) {
						$this->property_options_map[$property_id][$option_id] = [];
						$this->property_option_ids[] = $option_id;
					}
					
					$this->property_options_map[$property_id][$option_id][] = $product_id;
					
					
					break;
				case Property::PROPERTY_TYPE_NUMBER:
					if(!isset($this->property_numbers_map[$property_id])) {
						$this->property_ids[] =$property_id;
						$this->property_numbers_map[$property_id] = [];
					}
					$this->property_numbers_map[$property_id][] = $d['value']/1000;
					break;
				case Property::PROPERTY_TYPE_BOOL:
					$v = $d['value']?1:0;
					
					if(!isset($this->property_bool_map[$property_id])) {
						$this->property_ids[] =$property_id;
						$this->property_bool_map[$property_id] = [];
					}
					if(!isset($this->property_bool_map[$property_id][$v])) {
						$this->property_bool_map[$property_id][$v] = [];
					}
					$this->property_bool_map[$property_id][$v][] = $product_id;
					break;
			}
		}
		
	}
	
	public function getProductIds(): array
	{
		return $this->product_ids;
	}
	
	public function getBrandIds(): array
	{
		return $this->brand_ids;
	}
	
	public function getKindOfProductIds(): array
	{
		return $this->kind_of_product_ids;
	}
	
	public function getMinPrice(): float
	{
		return $this->min_price;
	}
	
	public function getMaxPrice(): float
	{
		return $this->max_price;
	}
	
	public function getPropertyIds(): array
	{
		return $this->property_ids;
	}
	
	public function getPropertyOptionIds( int $property_id=0): array
	{
		if(!$property_id) {
			return $this->property_option_ids;
		}
		
		return array_keys( $this->property_options_map[$property_id]??[] );
		
	}
	

	public function getBrandsMap(): array
	{
		return $this->brands_map;
	}
	
	public function getPropertyOptionsMap(): array
	{
		return $this->property_options_map;
	}
	
	public function getPropertyNumbersMap(): array
	{
		return $this->property_numbers_map;
	}
	
	public function getPropertyBoolMap(): array
	{
		return $this->property_bool_map;
	}

	
}