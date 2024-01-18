<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Product;
use JetApplication\Product_ShopData;

trait Core_Product_ShopData_Trait_Variants
{
	/**
	 * @var Product[]
	 */
	protected array|null $_variants = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $variant_master_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $variant_priority = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name of variant:'
	)]
	protected string $variant_name = '';
	
	
	
	public function getVariantName() : string
	{
		return $this->variant_name;
	}
	
	public function setVariantName( string $variant_name ) : void
	{
		$this->variant_name = $variant_name;
		$this->generateURLPathPart();
	}
	public function getFullName(): string
	{
		$name = $this->getName();
		$variant_name = $this->getVariantName();
		
		if($variant_name) {
			return $name.' '.$variant_name;
		}
		
		return $name;
	}
	
	public function getVariantMasterProduct() : ?static
	{
		return static::get(
			$this->getVariantMasterProductId(),
			$this->getShop()
		);
		
	}
	
	public function getVariantMasterProductId() : int
	{
		return $this->variant_master_product_id;
	}
	
	public function setVariantMasterProductId( int $variant_master_product_id ) : void
	{
		if($this->isVariant()) {
			$this->variant_master_product_id = $variant_master_product_id;
		}
	}
	

	public function getVariantPriority(): int
	{
		return $this->variant_priority;
	}
	

	public function setVariantPriority( int $variant_priority ): void
	{
		if($this->isVariant()) {
			$this->variant_priority = $variant_priority;
		}
	}
	
	
	
	/**
	 * @return static[]
	 */
	public function getVariants() : array
	{
		if($this->_variants===null) {
			$master_id = $this->getEntityId();
			if($this->isVariant()) {
				$master_id = $this->variant_master_product_id;
			}
			
			$where = static::getActiveQueryWhere( $this->getShop() );
			$where[] = 'AND';
			$where['variant_master_product_id'] = $master_id;
			
			
			/**
			 * @var DataModel_Fetch_Instances $_variants
			 */
			$_variants = static::fetchInstances($where);
			
			$_variants->getQuery()->setOrderBy(['variant_priority', 'variant_name']);
			
			$this->_variants=[];
			foreach($_variants as $variant) {
				$this->_variants[$variant->getEntityId()] = $variant;
			}
		}
		
		return $this->_variants;
	}
	
	public function syncVariant( Product_ShopData $variant_shop_data ) : void
	{
		$variant_shop_data->setKindId( $this->getKindId() );
		$variant_shop_data->setVatRate( $this->getVatRate() );
		$variant_shop_data->setName( $this->getName() );
		$variant_shop_data->setDescription( $this->getDescription() );
		$variant_shop_data->setShortDescription( $this->getShortDescription() );
		$variant_shop_data->setSeoTitle( $this->getSeoTitle() );
		$variant_shop_data->setSeoDescription( $this->getSeoDescription() );
		$variant_shop_data->setSeoKeywords( $this->getSeoKeywords() );
	}
	
	public function actualizeVariantMaster() : void
	{
		if($this->type!=Product::PRODUCT_TYPE_VARIANT_MASTER) {
			return;
		}
		
		$updated_price     = $this->actualizeVariantMaster_Price();
		$updated_stock_qty = $this->actualizeVariantMaster_InStockQty();
		$updated_avl       = $this->actualizeVariantMaster_Availability();
		
		
		if($updated_price || $updated_stock_qty || $updated_avl) {
			$this->save();
		}
		
	}
	
	public function actualizeVariantMaster_Price() : bool
	{
		if($this->type!=Product::PRODUCT_TYPE_VARIANT_MASTER) {
			return false;
		}
		
		$cheapest = null;
		foreach($this->getVariants() as $variant) {
			/**
			 * @var Product_ShopData $variant
			 */
			
			if(!$variant->isActive()) {
				continue;
			}
			

			if(!$cheapest) {
				$cheapest = $variant;
				continue;
			}
			
			if($cheapest->getPrice()>$variant->getPrice()) {
				$cheapest = $variant;
			}
		}
		
		if(!$cheapest) {
			return false;
		}
		
		$updated = false;
		
		if($cheapest->getPrice()!=$this->getPrice()) {
			$this->setPrice( $cheapest->getPrice() );
			$updated = true;
		}
		if($cheapest->getStandardPrice()!=$this->getStandardPrice()) {
			$this->setStandardPrice( $cheapest->getStandardPrice() );
			$updated = true;
		}
		
		return $updated;
	}
	
	public function actualizeVariantMaster_InStockQty() : bool
	{
		if($this->type!=Product::PRODUCT_TYPE_VARIANT_MASTER) {
			return false;
		}
		
		$best_stock_avl = null;
		
		$shop = $this->getShop();
		
		foreach($this->getVariants() as $variant) {
			/**
			 * @var Product_ShopData $variant
			 */
			if(!$variant->isActive()) {
				continue;
			}
			
			if(
				$best_stock_avl===null
			) {
				$best_stock_avl = $variant;
				
				continue;
			}
			
			if($best_stock_avl->getInStockQty()<$variant->getInStockQty()) {
				$best_stock_avl = $variant;
			}
			
		}
		
		if(!$best_stock_avl) {
			return false;
		}
		
		$updated = false;
		
		if($this->getInStockQty()!=$best_stock_avl->getInStockQty()) {
			$this->setInStockQty( $best_stock_avl->getInStockQty() );
			$updated = true;
		}
		
		return $updated;
	}
	
	public function actualizeVariantMaster_Availability() : bool
	{
		if($this->type!=Product::PRODUCT_TYPE_VARIANT_MASTER) {
			return false;
		}
		
		
		/**
		 * @var Product_ShopData $best_delivery
		 */
		$best_delivery = null;
		
		$shop = $this->getShop();
		
		foreach($this->getVariants() as $variant ) {
			/**
			 * @var Product_ShopData $variant
			 */
			if(!$variant->isActive()) {
				continue;
			}

			
			if( $best_delivery===null ) {
				$best_delivery = $variant;
				continue;
			}
			
			if(
				$variant->getAvailableFrom() ||
				$best_delivery->getAvailableFrom()
			) {
				if(
					!$best_delivery->getAvailableFrom() &&
					$variant->getAvailableFrom()
				) {
					$best_delivery = $variant;
					continue;
				}
				
				if(
					$best_delivery->getAvailableFrom() &&
					$variant->getAvailableFrom() &&
					$variant->getAvailableFrom() < $best_delivery->getAvailableFrom()
				) {
					$best_delivery = $variant;
					continue;
				}
				
				
				continue;
			}
			
			if(
				$variant->getLengthOfDelivery() <
				$best_delivery->getLengthOfDelivery()
			) {
				$best_delivery = $variant;
			}
		}
		
		if(!$best_delivery) {
			return false;
		}
		
		$updated = false;
		
		
		if($this->getAvailableFrom()!=$best_delivery->getAvailableFrom()) {
			$this->setAvailableFrom( $best_delivery->getAvailableFrom() );
			$updated = true;
		}
		
		if($this->getLengthOfDelivery()!=$best_delivery->getLengthOfDelivery()) {
			$this->setLengthOfDelivery( $best_delivery->getLengthOfDelivery() );
			$updated = true;
		}
		
		return $updated;
	}
	
	
}