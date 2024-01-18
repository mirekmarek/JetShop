<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\DataModel_Fetch_Instances;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Product;
use JetApplication\Product_Parameter_Value;
use JetApplication\Shops;


trait Core_Product_Trait_Variants
{
	/**
	 * @var Product[]
	 */
	protected array|null $_variants = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name of variant:'
	)]
	protected string $internal_name_of_variant = '';
	

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
	

	public function getInternalNameOfVariant(): string
	{
		return $this->internal_name_of_variant;
	}
	
	public function setInternalNameOfVariant( string $internal_name_of_variant ): void
	{
		$this->internal_name_of_variant = $internal_name_of_variant;
	}

	

	public function getVariantMasterProductId() : int
	{
		return $this->variant_master_product_id;
	}
	
	public function setVariantMasterProductId( int $variant_master_product_id ) : void
	{
		if($this->isVariant()) {
			$this->variant_master_product_id = $variant_master_product_id;
			
			foreach(Shops::getList() as $shop) {
				$this->getShopData( $shop )->setVariantMasterProductId( $variant_master_product_id );
			}
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
			foreach(Shops::getList() as $shop) {
				$this->getShopData( $shop )->setVariantPriority( $variant_priority );
			}
		}
	}
	
	
	
	
	/**
	 * @return static[]
	 */
	public function getVariants() : array
	{
		if($this->_variants===null) {
			/**
			 * @var DataModel_Fetch_Instances $_variants
			 */
			$_variants = static::fetchInstances(
				['variant_master_product_id'=>$this->id]
			);
			$_variants->getQuery()->setOrderBy(['variant_priority']);
			
			$this->_variants=[];
			foreach($_variants as $variant) {
				$this->_variants[$variant->getId()] = $variant;
			}
		}

		return $this->_variants;
	}

	public function addVariant( Product $variant ) : void
	{
		if(
			!$this->isVariantMaster() ||
			!$variant->isVariant()
		) {
			return;
		}
		
		$variants = $this->getVariants();
		
		$variant->setVariantPriority( count($variants)+1 );
		
		
		$this->syncVariant( $variant );
		$this->_variants[$variant->getId()] = $variant;
	}

	public function actualizeVariantMaster() : void
	{
		if($this->type!=static::PRODUCT_TYPE_VARIANT_MASTER) {
			return;
		}

		foreach($this->getVariants() as $v) {
			$this->syncVariant( $v );
		}
		
		foreach(Shops::getList() as $shop) {
			$this->getShopData($shop)->actualizeVariantMaster();
		}
		
		/** @noinspection PhpParamsInspection */
		Product_Parameter_Value::syncVariants(
			$this
		);
	}

	public function syncVariant( Product $variant ) : void
	{
		$variant->variant_master_product_id = $this->getId();
		$variant->type = static::PRODUCT_TYPE_VARIANT;
		
		$variant->kind_id = $this->kind_id;
		$variant->brand_id = $this->brand_id;
		$variant->supplier_id = $this->supplier_id;
		$variant->internal_name = $this->internal_name;
		$variant->delivery_class_id = $this->delivery_class_id;
		
		foreach( Shops::getList() as $shop ) {
			$variant->getShopData( $shop )->setVariantMasterProductId( $this->id );
			
			$this->getShopData( $shop )->syncVariant(
				$variant->getShopData( $shop )
			);
		}

		$variant->save();
	}


	public function actualizeVariant() : void
	{
	
		if(!$this->isVariant()) {
			return;
		}
		
		$master = static::load($this->variant_master_product_id);
		
		$master?->actualizeVariantMaster();

	}
}