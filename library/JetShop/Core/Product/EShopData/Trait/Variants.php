<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\JetShopEntity_Definition;
use JetApplication\Product;
use JetApplication\Product_EShopData;

trait Core_Product_EShopData_Trait_Variants
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
	#[JetShopEntity_Definition(
		is_description: true
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
			$this->getEshop()
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
			
			$where = static::getActiveQueryWhere( $this->getEshop() );
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
	
	public function syncVariant( Product_EShopData $variant_eshop_data ) : void
	{
		$variant_eshop_data->setKindId( $this->getKindId() );
		$variant_eshop_data->setName( $this->getName() );
		$variant_eshop_data->setDescription( $this->getDescription() );
		$variant_eshop_data->setShortDescription( $this->getShortDescription() );
		$variant_eshop_data->setSeoTitle( $this->getSeoTitle() );
		$variant_eshop_data->setSeoDescription( $this->getSeoDescription() );
		$variant_eshop_data->setSeoKeywords( $this->getSeoKeywords() );
	}
}