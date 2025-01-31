<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Availabilities;
use JetApplication\Availability;
use JetApplication\EShopEntity_Basic;
use JetApplication\Product;
use JetApplication\Product_Availability;
use JetApplication\Product_SetItem;

#[DataModel_Definition(
	name: 'products_availability',
	database_table_name: 'products_availability'
)]
abstract class Core_Product_Availability extends EShopEntity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $availability_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $length_of_delivery = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE,
	)]
	protected Data_DateTime|null $available_from = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		is_key: true,
	)]
	protected float $number_of_available = 0;
	
	protected bool $do_not_actualize_references = false;

	
	protected static array $loaded = [];
	
	public static function getInStockQtyMap( Availability $availability ) : array
	{
		$where = [
			'availability_code' => $availability->getCode()
		];
		
		
		return static::dataFetchPairs(
			select: [
				'product_id',
				'in_stock_qty'
			],
			where: $where
		);
	}
	
	public static function get( Availability $availability, int $product_id ) : static
	{
		$key = $availability->getCode().':'.$product_id;
		if(!isset(static::$loaded[$key])) {
			$price = static::load([
				'availability_code' => $availability->getCode(),
				'AND',
				'product_id' => $product_id
			]);
			
			if(!$price) {
				$price = new static();
				$price->setAvailabilityCode( $availability->getCode() );
				$price->setProductId( $product_id );
			}
			
			static::$loaded[$key] = $price;
		}
		
		return static::$loaded[$key];
	}
	
	public static function filterIsInStock(
		Availability $availability,
		bool         $is_in_stock,
		?array       $product_ids = null
	) : array
	{
		
		$where = [
			'availability_code' => $availability->getCode()
		];
		
		if($product_ids!==null) {
			$where[] = 'AND';
			$where['product_id'] = $product_ids;
		}
		
		$where[] = 'AND';
		if($is_in_stock) {
			$where[] = ['number_of_available >'=>0];
		} else {
			$where[] = ['number_of_available'=>0];
		}
		
		
		$data = static::dataFetchAll(
			select: ['product_id'],
			where: $where,
			raw_mode: true
		);
		
		$filter_result = [];
		foreach($data as $d) {
			$filter_result[] = (int)$d['product_id'];
		}
		
		return $filter_result;
	}
	
	
	public function getAvailability() : Availability
	{
		return Availabilities::get( $this->availability_code );
	}
	
	public function getAvailabilityCode(): string
	{
		return $this->availability_code;
	}
	
	public function setAvailabilityCode( string $availability_code ): void
	{
		$this->availability_code = $availability_code;
	}
	

	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ): void
	{
		$this->product_id = $product_id;
	}
	

	public function getLengthOfDelivery(): int
	{
		return $this->length_of_delivery;
	}

	public function setLengthOfDelivery( int $length_of_delivery ): void
	{
		$this->length_of_delivery = $length_of_delivery;
		$this->save();
	}
	
	public function getAvailableFrom(): ?Data_DateTime
	{
		if($this->available_from<Data_DateTime::now()) {
			return null;
		}
		
		return $this->available_from;
	}
	
	public function setAvailableFrom( Data_DateTime|string|null $available_from ): void
	{
		$this->available_from = Data_DateTime::catchDate( $available_from );
		$this->save();
	}
	

	public function getNumberOfAvailable(): float
	{
		return $this->number_of_available;
	}
	

	public function setNumberOfAvailable( float $number_of_available ): void
	{
		$this->number_of_available = $number_of_available;
		$this->save();
	}
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		$this->actualizeReferences();
	}
	
	public function afterUpdate(): void
	{
		parent::afterUpdate();
		$this->actualizeReferences();
	}
	
	
	public function actualizeReferences() : void
	{
		if($this->do_not_actualize_references) {
			$this->do_not_actualize_references = false;
			return;
		}
		
		switch( Product::getProductType($this->product_id) ) {
			case Product::PRODUCT_TYPE_REGULAR:
				foreach( Product_SetItem::getSetIds($this->product_id)  as $set_id ) {
					static::get( $this->getAvailability(), $set_id )?->actualizeSet();
				}
				break;
			case Product::PRODUCT_TYPE_SET:
				$this->actualizeSet();
				break;
			case Product::PRODUCT_TYPE_VARIANT:
				static::get(
					$this->getAvailability(),
					Product::getProductVariantMasterProductId( $this->product_id )
				)?->actualizeVariantMaster();
				break;
			case Product::PRODUCT_TYPE_VARIANT_MASTER:
				$this->actualizeVariantMaster();
				break;
		}
	}
	
	
	public function actualizeSet() : bool
	{
		$availability = $this->getAvailability();
		
		$worst_stock_avl = null;
		$worst_delivery = null;
		
		
		foreach(Product_SetItem::getProductSetItems( $this->product_id ) as $set_item) {
			$set_item_id = $set_item->getItemProductId();
			
			$set_item_availability = Product_Availability::get( $availability, $set_item_id );
			
			$stock_avl = floor($set_item_availability->getNumberOfAvailable()/$set_item->getCount());
			
			if(
				$worst_stock_avl===null ||
				$worst_stock_avl>$stock_avl
			) {
				$worst_stock_avl = $stock_avl;
			}
			
			
			if( $worst_delivery===null ) {
				$worst_delivery = $set_item_availability;
				continue;
			}
			
			if(
				$set_item_availability->getAvailableFrom() ||
				$worst_delivery->getAvailableFrom()
			) {
				if(
					!$worst_delivery->getAvailableFrom() &&
					$set_item_availability->getAvailableFrom()
				) {
					$worst_delivery = $set_item_availability;
					continue;
				}
				
				if(
					$worst_delivery->getAvailableFrom() &&
					$set_item_availability->getAvailableFrom() &&
					$set_item_availability->getAvailableFrom() > $worst_delivery->getAvailableFrom()
				) {
					$worst_delivery = $set_item_availability;
					continue;
				}
				
				
				continue;
			}
			
			if(
				$set_item_availability->getLengthOfDelivery() >
				$worst_delivery->getLengthOfDelivery()
			) {
				$worst_delivery = $set_item_availability;
			}
			
			
		}
		
		$updated = [];
		
		
		if($worst_delivery) {
			if($this->getNumberOfAvailable()!=$worst_stock_avl) {
				$this->number_of_available = $worst_stock_avl;
				$updated['number_of_available'] = $this->number_of_available;
			}
			
			if($this->getAvailableFrom()!=$worst_delivery->getAvailableFrom()) {
				$this->available_from = $worst_delivery->getAvailableFrom();
				$updated['available_from'] = $this->available_from;
			}
			
			if($this->getLengthOfDelivery()!=$worst_delivery->getLengthOfDelivery()) {
				$this->length_of_delivery = $worst_delivery->getLengthOfDelivery();
				$updated['length_of_delivery'] = $this->length_of_delivery;
			}
		}
		
		if($updated) {
			$this->do_not_actualize_references = true;
			$this->save();
		}
		
		
		return (bool)$updated;
	}
	
	public function actualizeVariantMaster() : bool
	{
		$best_stock_avl = null;
		$best_delivery_avl = null;
		
		$availability = $this->getAvailability();
		
		
		$variant_ids = Product::getProductActiveVariantIds( $this->product_id );
		
		foreach($variant_ids as $variant_id) {
			
			$variant_avl = Product_Availability::get( $availability, $variant_id );
			
			if( $best_stock_avl===null ) {
				$best_stock_avl = $variant_avl;
			} else {
				if($best_stock_avl->getNumberOfAvailable()<$variant_avl->getNumberOfAvailable()) {
					$best_stock_avl = $variant_avl;
				}
			}
			
			if( $best_delivery_avl===null ) {
				$best_delivery_avl = $variant_avl;
				continue;
			}
			
			if(
				$variant_avl->getAvailableFrom() ||
				$best_delivery_avl->getAvailableFrom()
			) {
				if(
					!$best_delivery_avl->getAvailableFrom() &&
					$variant_avl->getAvailableFrom()
				) {
					$best_delivery_avl = $variant_avl;
					continue;
				}
				
				if(
					$best_delivery_avl->getAvailableFrom() &&
					$variant_avl->getAvailableFrom() &&
					$variant_avl->getAvailableFrom() < $best_delivery_avl->getAvailableFrom()
				) {
					$best_delivery_avl = $variant_avl;
					continue;
				}
				
				
				continue;
			}
			
			if(
				$variant_avl->getLengthOfDelivery()
				<
				$best_delivery_avl->getLengthOfDelivery()
			) {
				$best_delivery_avl = $variant_avl;
			}
			
			
			
		}
		
		
		$updated = [];
		
		if($best_stock_avl) {
			if($this->getNumberOfAvailable()!=$best_stock_avl->getNumberOfAvailable()) {
				$this->number_of_available = $best_stock_avl->getNumberOfAvailable();
				$updated['number_of_available'] = $this->number_of_available;
			}
			
			if($this->getAvailableFrom()!=$best_delivery_avl->getAvailableFrom()) {
				$this->available_from = $best_delivery_avl->getAvailableFrom();
				$updated['available_from'] = $this->available_from;
			}
			
			if($this->getLengthOfDelivery()!=$best_delivery_avl->getLengthOfDelivery()) {
				$this->length_of_delivery = $best_delivery_avl->getLengthOfDelivery();
				$updated['length_of_delivery'] = $this->length_of_delivery;
			}
		}
		
		if($updated) {
			$this->do_not_actualize_references = true;
			$this->save();
		}
		
		
		return (bool)$updated;
	}
	
}