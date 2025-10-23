<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShop;
use JetApplication\KindOfProduct_Property;
use JetApplication\Product_EShopData;
use JetApplication\Product_Parameter_Value;
use JetApplication\Property;
use JetApplication\Property_EShopData;
use JetApplication\Property_Options_Option_EShopData;
use JetApplication\Exports_ProductParams_Item;

abstract class Core_Exports_ProductParams {
	protected EShop $eshop;
	
	/**
	 * @var array<int,Property_EShopData>
	 */
	protected array $all_properties;
	
	/**
	 * @var array<int,Property_Options_Option_EShopData>
	 */
	protected array $all_property_options;
	
	protected array $properties_map;
	
	public function __construct( EShop $eshop )
	{
		$this->eshop = $eshop;
		
		$this->all_properties = Property_EShopData::getAllActive( $eshop );
		$this->all_property_options = Property_Options_Option_EShopData::getAllActive( $eshop );
		$this->properties_map = [];
		$_map = KindOfProduct_Property::dataFetchAll(
			select: ['kind_of_product_id', 'property_id'],
			where: [
				'show_on_product_detail' => true
			]
		);
		foreach($_map as $m) {
			$kind_of_product_id = $m['kind_of_product_id'];
			$property_id = $m['property_id'];
			$this->properties_map[$kind_of_product_id][$property_id] = $property_id;
		}
	}
	
	/**
	 * @param Product_EShopData $product
	 * @return array<Exports_ProductParams_Item>
	 */
	public function get( Product_EShopData $product ) : array
	{
		$res = [];
		$property_ids = $this->properties_map[$product->getKindId()] ?? [];
		if(!$property_ids) {
			return [];
		}
		
		
		$product_values = Product_Parameter_Value::getForProduct( $product->getId() );
			
		$values = [];
		foreach($product_values as $property_id=>$_values) {
			foreach($_values as $_v) {
				$values[$property_id][] = $_v->getValue();
			}
		}
		
		
		foreach($property_ids as $propery_id) {
			$property = $this->all_properties[$propery_id] ?? null;
			if(!$property) {
				continue;
			}
			
			$v = $values[$property->getId()] ?? [];
			if(
				!isset($v[0]) ||
				$v[0]==='' ||
				$v[0]==='0'
			) {
				continue;
			}

			$value = null;
			
			switch($property->getType()) {
				case Property::PROPERTY_TYPE_BOOL:
					$value = (bool)$value;
					break;
				case Property::PROPERTY_TYPE_OPTIONS:
					$value = [];
					foreach($v as $o_id) {
						$option = $all_property_options[$o_id]??null;
						if($option) {
							$value[] = $option->getProductDetailLabel();
						}
					}
					
					if(!$value) {
						continue 2;
					}
					
					break;
				case Property::PROPERTY_TYPE_NUMBER:
					$value = $v[0];

					$value = $value/1000;
					
					if($property->getDecimalPlaces()) {
						$value = round( $value, $property->getDecimalPlaces() );
					} else {
						$value = floor( $value );
					}

					break;
				case Property::PROPERTY_TYPE_TEXT:
					$value = $v[0];
					
					break;
			}
			
			$res[] = new Exports_ProductParams_Item(
				name: $property->getLabel(),
				value: $value,
				units: $property->getUnits(),
			);
			
		}

		
		return $res;
	}
	
	
}