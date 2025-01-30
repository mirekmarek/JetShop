<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;


use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\Property;
use JetApplication\Product;

#[DataModel_Definition(
	name: 'products_text_parameters',
	database_table_name: 'products_text_parameters',
)]
abstract class Core_Product_Parameter_TextValue extends EShopEntity_WithEShopRelation
{
	
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
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $text = '';
	
	
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
	
	public function getText(): string
	{
		return $this->text;
	}
	
	public function setText( string $text ): void
	{
		$this->text = $text;
	}
	

	
	/**
	 * @param int $product_id
	 * @return static[][]
	 */
	public static function getForProduct( int $product_id ): array
	{
		$data = static::fetch(
			[
				'products_text_parameters' => [
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
			
			$map[$property_id][$item->getEshopKey()] = $item;
		}
		
		return $map;
	}
	
	public static function syncSetItemsParameters( int $set_product_id, array $set_item_ids ): void
	{
		
		if( !$set_item_ids ) {
			return;
		}
		
		$readMap = function( int|array $id ): array {
			$_map = static::fetchInstances(
				where: [
					'product_id' => $id
				]
			);
			
			$map = [];
			foreach( $_map as $d ) {
				$key = $d->getProductId() . ':' . $d->getEshopKey().':'.$d->getText();
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
			/**
			 * @var static $param
			 */
			$param_item = new static();
			$param_item->setEshop( $param->getEshop() );
			$param_item->setProductId( $set_product_id );
			$param_item->setPropertyId( $param->getPropertyId() );
			$param_item->setText( $param->getText() );
			$param_item->save();
		}
		
		
	}
	
	public static function syncVariants( Product $variant_master ): void
	{
		if(!$variant_master->isVariantMaster()) {
			return;
		}
		
		$readMap = function( int|array $id ): array {
			$_map = static::fetchInstances(
				where: [
					'product_id' => $id
				]
			);
			
			$map = [];
			foreach( $_map as $d ) {
				$property_id = $d->getPropertyId();
				$eshop_key = $d->getEshopKey();
				
				if(!isset($map[$property_id])) {
					$map[$property_id] = [];
				}
				
				$map[$property_id][$eshop_key] = $d;
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
			foreach( $values as $eshop_key=>$master_value ) {
				/**
				 * @var static $master_value
				 */
				foreach($variant_maps as $variant_id=>$variant_values) {
					if(!isset($variant_maps[$variant_id][$property_id][$eshop_key])) {
						$variant_value = new static();
						$variant_value->setEshop( $master_value->getEshop() );
						$variant_value->setPropertyId( $property_id );
						$variant_value->setProductId( $variant_id );
						$variant_value->setText( $master_value->getText() );
						$variant_value->save();
						
						continue;
					}
					
					/**
					 * @var static $variant_value
					 */
					$variant_value = $variant_maps[$variant_id][$property_id][$eshop_key];
					if($variant_value->getText()!=$master_value->getText()) {
						$variant_value->setText( $master_value->getText() );
						$variant_value->save();
					}
				}
			}
		}

		
		
	}
}