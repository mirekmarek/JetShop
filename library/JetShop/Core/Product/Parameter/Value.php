<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;


use JetApplication\KindOfProduct;
use JetApplication\Product_Parameter_TextValue;
use JetApplication\Property;
use JetApplication\Product;

#[DataModel_Definition(
	name: 'products_parameters',
	database_table_name: 'products_parameters',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
)]
abstract class Core_Product_Parameter_Value extends DataModel
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $property_id = 0;
	
	protected Property|null $property = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $value = 0;
	
	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ): void
	{
		$this->product_id = $product_id;
	}
	
	public function getPropertyId(): int
	{
		return $this->property_id;
	}
	
	public function setPropertyId( int $property_id ): void
	{
		$this->property_id = $property_id;
	}
	
	public function getValue(): int
	{
		return $this->value;
	}
	
	public function setValue( string $value ): void
	{
		$this->value = $value;
	}
	
	/**
	 * @param int $product_id
	 * @return static[][]
	 */
	public static function getForProduct( int $product_id ): array
	{
		$data = static::fetch(
			[
				'' => [
					'product_id' => $product_id
				]
			]
		);
		
		$map = [];
		
		foreach( $data as $item ) {
			$property_id = $item->property_id;
			if( !isset( $map[$property_id] ) ) {
				$map[$property_id] = [];
			}
			
			$map[$property_id][] = $item;
		}
		
		return $map;
	}
	
	public static function syncSetItemsParameters( int $set_product_id, array $set_item_ids ): void
	{
		if( !$set_item_ids ) {
			return;
		}
		
		$readMap = function( int|array $id ): array {
			$_map = static::dataFetchAll(
				select: [
					'property_id',
					'value',
				],
				where: [
					'product_id' => $id
				]
			);
			
			$map = [];
			foreach( $_map as $d ) {
				$key = $d['property_id'] . ':' . $d['value'];
				$map[$key] = $d;
			}
			
			return $map;
		};
		
		$getHash = function( array $map ): string {
			$hash = array_keys( $map );
			asort( $hash );
			$hash = implode( ';', $hash );
			
			return $hash;
		};
		
		
		$current_parameters = $readMap( $set_product_id );
		$parameters_of_items = $readMap( $set_item_ids );
		
		
		if(
			$getHash( $current_parameters ) == $getHash( $parameters_of_items )
		) {
			return;
		}
		
		
		static::dataDelete( where: [
			'product_id' => $set_product_id
		] );
		
		
		foreach( $parameters_of_items as $param ) {
			$param_item = new static();
			$param_item->setProductId( $set_product_id );
			$param_item->setPropertyId( $param['property_id'] );
			$param_item->setValue( $param['value'] );
			$param_item->save();
		}
		
		Product_Parameter_TextValue::syncSetItemsParameters( $set_product_id, $set_item_ids );
	}
	
	public static function syncVariants( Product $variant_master ): void
	{
		if(!$variant_master->isVariantMaster()) {
			return;
		}
		
		$kind = KindOfProduct::load( $variant_master->getKindId() );
		
		$variant_selector_property_ids = $kind?->getVariantSelectorPropertyIds()??[];
		
		$readMap = function( int|array $id ): array {
			$_map = static::dataFetchAll(
				select: [
					'property_id',
					'value',
				],
				where: [
					'product_id' => $id
				]
			);
			
			$map = [];
			foreach( $_map as $d ) {
				$property_id = (int)$d['property_id'];
				$value = (int)$d['value'];
				
				if(!isset($map[$property_id])) {
					$map[$property_id] = [];
				}
				
				$map[$property_id][] = $value;
			}
			
			return $map;
		};
		
		
		$master_id = $variant_master->getId();
		$variant_ids = array_keys($variant_master->getVariants());
		
		$master_map = $readMap( $master_id );
		$variant_maps = [];
		foreach($variant_ids as $variant_id) {
			$variant_maps[$variant_id] = $readMap( $variant_id );
		}
		
		foreach($master_map as $property_id=>$values) {
			if(in_array($property_id, $variant_selector_property_ids)) {
				$master_map[$property_id] = [];
				continue;
			}
			
			foreach($variant_maps as $variant_id=>$d) {
				$variant_maps[$variant_id][$property_id] = $values;
			}
		}
		
		foreach($variant_maps as $variant_id=>$variant_properties) {
			foreach($variant_selector_property_ids as $selector_property_id) {
				if(!isset($master_map[$selector_property_id])) {
					$master_map[$selector_property_id] = $variant_properties[$selector_property_id];
				} else {
					$master_map[$selector_property_id] = array_merge(
						$master_map[$selector_property_id],
						$variant_properties[$selector_property_id]
					);
				}
			}
		}
		
		foreach($variant_selector_property_ids as $selector_property_id) {
			$master_map[$selector_property_id] = array_unique( $master_map[$selector_property_id] );
		}
		
		static::dataDelete( where: [
			'product_id' => array_merge([$master_id], $variant_ids)
		] );
		
		foreach($master_map as $property_id=>$values) {
			foreach($values as $value) {
				$param_item = new static();
				$param_item->setProductId( $master_id );
				$param_item->setPropertyId( $property_id );
				$param_item->setValue( $value );
				$param_item->save();
				
			}
		}
		
		foreach($variant_maps as $variant_id=>$variant_properties) {
			foreach($variant_properties as $property_id=>$values) {
				foreach($values as $value) {
					$param_item = new static();
					$param_item->setProductId( $variant_id );
					$param_item->setPropertyId( $property_id );
					$param_item->setValue( $value );
					$param_item->save();
					
				}
			}
		}
		
		Product_Parameter_TextValue::syncVariants( $variant_master );
	}
	
	public function clone( int $cloned_product_id ) : void
	{
		$clon = clone $this;
		$clon->setIsNew( true );
		$clon->id = 0;
		$clon->product_id = $cloned_product_id;
		
		$clon->save();
	}
}