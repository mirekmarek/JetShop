<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\EShopEntity_Marketing;
use JetApplication\Product;
use JetApplication\Product_Relation;
use JetApplication\ProductFilter;
use JetApplication\Product_RelevantRelation;

trait Core_EShopEntity_HasProductsRelation_Trait {
	
	public const RELEVANCE_MODE_ALL = 'all';
	public const RELEVANCE_MODE_BY_FILTER = 'by_filter';
	public const RELEVANCE_MODE_ALL_BUT_FILTER = 'all_but_filter';
	public const RELEVANCE_MODE_ONLY_PRODUCTS = 'only_products';
	public const RELEVANCE_MODE_ALL_BUT_PRODUCTS = 'all_but_products';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Relevance mode:',
		select_options_creator: [
			EShopEntity_Marketing::class,
			'getRelevanceModeScope'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Invalid value',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected string $relevance_mode = self::RELEVANCE_MODE_ALL;
	
	protected ?array $product_ids = null;
	protected ?ProductFilter $product_filter = null;
	
	public static function getRelevanceModeScope() : array
	{
		return [
			static::RELEVANCE_MODE_ALL              => Tr::_('All products'),
			static::RELEVANCE_MODE_BY_FILTER        => Tr::_('By filter'),
			static::RELEVANCE_MODE_ALL_BUT_FILTER   => Tr::_('All but filter'),
			static::RELEVANCE_MODE_ONLY_PRODUCTS    => Tr::_('Only products'),
			static::RELEVANCE_MODE_ALL_BUT_PRODUCTS => Tr::_('All but products'),
		];
	}
	
	
	public function getRelevanceMode(): string
	{
		return $this->relevance_mode;
	}
	
	public function setRelevanceMode( string $relevance_mode ): void
	{
		$this->relevance_mode = $relevance_mode;
	}
	
	
	protected function getProductFilterInitialProductIds() : array
	{
		return Product::dataFetchCol(
			select: ['id'],
			where: [
				'archived' => false
			]
		);
	}
	
	
	public function getProductsFilter() : ProductFilter
	{
		if($this->product_filter===null) {
			$this->product_filter = new ProductFilter( $this->getEshop() );
			$this->product_filter->setContextEntity( static::getEntityType() );
			$this->product_filter->setContextEntityId( $this->id );
			$this->product_filter->setInitialProductIds( $this->getProductFilterInitialProductIds() );
			$this->product_filter->load();
			
		}
		
		return $this->product_filter;
	}
	
	public function getProductIds() : array|bool
	{
		if($this->product_ids===null) {
			switch( $this->getRelevanceMode() ) {
				case static::RELEVANCE_MODE_ALL:
					$this->product_ids = [];
					break;
				case static::RELEVANCE_MODE_ONLY_PRODUCTS:
				case static::RELEVANCE_MODE_ALL_BUT_PRODUCTS:
					$this->product_ids = Product_Relation::get( $this );
					break;
				case static::RELEVANCE_MODE_BY_FILTER:
				case static::RELEVANCE_MODE_ALL_BUT_FILTER:
					$filter = $this->getProductsFilter();
					$this->product_ids = $filter->filter();
					break;
				
			}
		}
		
		return $this->product_ids;
	}
	
	protected ?array $relevant_product_ids = null;
	
	public function getRelevantProductIds( bool $process=false ) : array
	{
		if($this->relevant_product_ids===null) {
			if($process) {
				$getAllProducts = function() {
					return Product::dataFetchCol(
						select: ['id'],
						where: [
							'archived' => false,
						],
						raw_mode: true
					);
				};
				
				$this->relevant_product_ids = [];

				switch( $this->getRelevanceMode() ) {
					case static::RELEVANCE_MODE_ALL:
						$this->relevant_product_ids = $getAllProducts();
						break;
					case static::RELEVANCE_MODE_ONLY_PRODUCTS:
						$this->relevant_product_ids = Product_Relation::get( $this );
						break;
					case static::RELEVANCE_MODE_ALL_BUT_PRODUCTS:
						$this->relevant_product_ids = Product_Relation::get( $this );
						if($this->relevant_product_ids) {
							$this->relevant_product_ids = array_diff( $getAllProducts(), $this->relevant_product_ids );
						}
						
						break;
					case static::RELEVANCE_MODE_BY_FILTER:
						$this->relevant_product_ids = $this->getProductsFilter()->filter();
						break;
					case static::RELEVANCE_MODE_ALL_BUT_FILTER:
						$this->relevant_product_ids = $this->getProductsFilter()->filter();
						if($this->relevant_product_ids) {
							$this->relevant_product_ids = array_diff( $getAllProducts(), $this->relevant_product_ids );
						}
						
						break;
				}
			} else {
				$this->relevant_product_ids = Product_RelevantRelation::get( $this );
			}
			
		}
		
		return $this->relevant_product_ids;
	}
	
	
	
	public function addProduct( int $product_id ) : bool
	{
		$ids = $this->getProductIds();
		if(in_array($product_id, $ids)) {
			return false;
		}
		
		Product_Relation::add( $this, $product_id );
		
		$this->product_ids[] = $product_id;
		
		return true;
	}
	
	public function removeProduct( int $product_id ) : bool
	{
		Product_Relation::remove( $this, $product_id );
		
		$this->product_ids = null;
		
		return true;
	}
	
	public function removeAllProducts() : bool
	{
		Product_Relation::removeAll( $this );
		
		$this->product_ids = null;
		
		return true;
	}
	
	public function isRelevant( array $product_ids ) : bool
	{
		if($this->getRelevanceMode()===static::RELEVANCE_MODE_ALL) {
			return true;
		}
		
		return (bool)array_intersect($this->getRelevantProductIds(), $product_ids);
	}
	
	
	public function actualizeRelevantRelations() : void
	{
		$new_product_ids = $this->getRelevantProductIds( true );
		
		$current_product_ids = Product_RelevantRelation::get( $this );
		
		echo $this->getId()."\n";
		
		
		foreach( $new_product_ids as $i=>$product_id) {
			if(!in_array($product_id, $current_product_ids)) {
				echo "\t ADD {$product_id}\n";
				
				Product_RelevantRelation::add(
					$this,
					$product_id,
				);
			}
		}
		
		
		foreach($current_product_ids as $product_id) {
			if(!in_array($product_id, $new_product_ids)) {
				echo "\t DEL {$product_id}\n";
				
				Product_RelevantRelation::remove(
					$this,$product_id
				);
				
			}
		}
	}
	
	public static function actualizeAllRelevantRelations() : void
	{
		$list = static::fetchInstances(
			[
				'is_active' => true,
			]
		);
		foreach($list as $gift) {
			$gift->actualizeRelevantRelations();
		}
	}
	
}