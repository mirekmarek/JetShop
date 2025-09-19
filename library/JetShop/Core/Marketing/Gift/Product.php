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
use JetApplication\EShop;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Application_Service_Admin_Marketing_GiftsProducts;
use JetApplication\EShopEntity_Marketing;
use JetApplication\EShopEntity_Definition;
use JetApplication\Marketing_Gift;
use JetApplication\Marketing_Gift_Product;
use JetApplication\Product_Availability;
use JetApplication\Product_EShopData;
use JetApplication\Product_RelevantRelation;
use JetApplication\ShoppingCart;

#[DataModel_Definition(
	name: 'gift_product',
	database_table_name: 'gifts_product',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Gift - product',
	admin_manager_interface: Application_Service_Admin_Marketing_GiftsProducts::class
)]
abstract class Core_Marketing_Gift_Product extends EShopEntity_Marketing implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN,
	)]
	protected int $gift_product_id = 0;
	
	protected Product_EShopData|null|bool $gift_product = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:',
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Product price limit - min:',
	)]
	protected float $product_price_limit_min = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Product price limit - max:',
	)]
	protected float $product_price_limit_max = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'In stock limit:',
	)]
	protected int $in_stock_limit = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Only one piece of gift',
	)]
	protected bool $only_one = false;
	
	
	public function getGiftProductId(): int
	{
		return $this->gift_product_id;
	}
	
	public function setGiftProductId( int $gift_product_id ): void
	{
		$this->gift_product_id = $gift_product_id;
	}
	
	public function getGiftProduct(): ?Product_EShopData
	{
		if($this->gift_product===null) {
			$this->gift_product = Product_EShopData::get( $this->gift_product_id, $this->getEshop() );
			if(!$this->gift_product) {
				$this->gift_product = false;
			}
		}
		return $this->gift_product ? : null;
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
	
	public function getProductPriceLimitMin(): float
	{
		return $this->product_price_limit_min;
	}
	
	public function setProductPriceLimitMin( float $product_price_limit_min ): void
	{
		$this->product_price_limit_min = $product_price_limit_min;
	}
	
	public function getProductPriceLimitMax(): float
	{
		return $this->product_price_limit_max;
	}
	
	public function setProductPriceLimitMax( float $product_price_limit_max ): void
	{
		$this->product_price_limit_max = $product_price_limit_max;
	}
	
	public function getInStockLimit(): int
	{
		return $this->in_stock_limit;
	}
	
	public function setInStockLimit( int $in_stock_limit ): void
	{
		$this->in_stock_limit = $in_stock_limit;
	}
	
	public function getOnlyOne(): bool
	{
		return $this->only_one;
	}
	
	public function setOnlyOne( bool $only_one ): void
	{
		$this->only_one = $only_one;
	}
	
	/**
	 * @param Product_EShopData $product
	 * @return array<int,static>
	 */
	public static function getProductGifts( Product_EShopData $product ): array
	{
		$gift_ids = Product_RelevantRelation::dataFetchCol(
			select: ['entity_id'],
			where: [
				'entity_type' => static::getEntityType(),
				'AND',
				'product_id'=>$product->getId(),
			],
			raw_mode: true
		);
		
		
		$all_gifts = static::getAvailable( $product->getEshop() );
		
		$gifts = [];
		
		foreach($all_gifts as $gift) {
			if(!in_array($gift->getId(), $gift_ids)) {
				continue;
			}
			
			
			if($gift->productIsRelevant( $product )) {
				$gifts[$gift->getId()] = $gift;
			}
		}
		
		return $gifts;
	}
	
	protected function productIsRelevant( Product_EShopData $product ) : bool
	{
		if(
			$this->product_price_limit_min==0 &&
			$this->product_price_limit_max==0
		) {
			return true;
		}
		
		$price = $product->getPrice( $product->getEshop()->getDefaultPricelist() );
		
		if(
			$this->product_price_limit_min>0 &&
			$price<$this->product_price_limit_min
		) {
			return false;
		}
		
		if(
			$this->product_price_limit_max>0 &&
			$price>$this->product_price_limit_max
		) {
			return false;
		}
		
		return true;
	}
	
	protected static array $available = [];
	
	
	
	protected function _isAvailable( ShoppingCart $cart ) : bool
	{
		if(
			$this->getInStockLimit()>0 &&
			$this->getGiftProduct()->getNumberOfAvailable( $cart->getAvailability() ) < $this->getInStockLimit()
		) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param EShop $eshop
	 *
	 * @return static[]
	 */
	public static function getAvailable( EShop $eshop ) : array
	{
		$key = $eshop->getKey();
		
		if(!isset( static::$available[$key])) {
			static::$available[$key] = [];
			
			$active_gift_product_ids = static::dataFetchCol(
				select: ['gift_product_id'],
				where: $eshop->getWhere(),
				raw_mode: true
			);
			
			
			if($active_gift_product_ids) {
				$active_gift_product_ids = Product_EShopData::dataFetchCol(
					select: ['entity_id'],
					where: [
						Product_EShopData::getActiveQueryWhere( $eshop ),
						'AND',
						'entity_id' => $active_gift_product_ids
					]
				);
			}
			
			if($active_gift_product_ids) {
				$where = [
					static::getActiveQueryWhere( $eshop ),
					'AND',
					'gift_product_id' => $active_gift_product_ids
				];
				
				$in_stock_map = Product_Availability::getInStockQtyMap( $eshop->getDefaultAvailability() );
				
				$list = static::fetch(
					where_per_model: [ '' => $where],
					order_by: 'priority',
					item_key_generator: function( Marketing_Gift_Product $item ) : int {
						return $item->getGiftProductId();
					}
				);
				
				
				foreach($list as $id=>$gift) {
					if(
						$gift->getInStockLimit() &&
						$gift->getInStockLimit() > $in_stock_map[$gift->getGiftProductId()]??0
					) {
						unset($list[$id]);
					}
				}
				
				static::$available[$key] = $list;
			}
			
		}
		
		return static::$available[$key];
	}
	
	
	public function createCommonGift( ShoppingCart $cart, float $number_of_units = 1 ) : Marketing_Gift
	{
		$gift = new Marketing_Gift( $cart, $this->gift_product_id, $number_of_units );
		$gift->setOnlyOne( $this->getOnlyOne() );
		$gift->setInStockLimit( $this->getInStockLimit() );
		
		return $gift;
	}
	
	public function hasImages(): bool
	{
		return false;
	}
	
}