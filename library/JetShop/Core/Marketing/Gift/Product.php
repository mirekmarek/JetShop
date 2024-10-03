<?php
namespace JetShop;

use Jet\DataModel;

use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_Marketing;
use JetApplication\Marketing_Gift;
use JetApplication\Marketing_Gift_Product;
use JetApplication\Product_ShopData;
use JetApplication\ShoppingCart;

#[DataModel_Definition(
	name: 'gift_product',
	database_table_name: 'gifts_product',
)]
abstract class Core_Marketing_Gift_Product extends Entity_Marketing
{
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN,
	)]
	protected int $gift_product_id = 0;
	
	protected Product_ShopData|null|bool $gift_product = null;
	
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
	
	public function getGiftProduct(): ?Product_ShopData
	{
		if($this->gift_product===null) {
			$this->gift_product = Product_ShopData::get( $this->gift_product_id, $this->getShop() );
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
	
	public function isAvailable( ShoppingCart $cart ) : bool
	{
		if(
			!$this->isActive() ||
			!$this->getGiftProduct()
		) {
			return false;
		}
		
		if(
			$this->getInStockLimit()>0 &&
			$this->getGiftProduct()->getNumberOfAvailable( $cart->getAvailability() ) < $this->getInStockLimit()
		) {
			return false;
		}
		
		return true;
	}
	
	public function productIsRelevant( ShoppingCart $cart, Product_ShopData $product ) : bool
	{
		if(!$this->isRelevant([$product->getId()])) {
			return false;
		}
		
		if(
			$this->product_price_limit_min==0 &&
			$this->product_price_limit_max==0
		) {
			return true;
		}
		
		$price = $product->getPrice( $cart->getPricelist() );
		
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
	
	/**
	 * @param ShoppingCart $cart
	 *
	 * @return static[]
	 */
	public static function getAvailable( ShoppingCart $cart ) : array
	{
		$shop = $cart->getShop();
		
		$key = $shop->getKey().':'.$cart->getAvailability()->getCode().':'.$cart->getPricelist()->getCode();
		
		if(!isset( static::$available[$key])) {
			$where = static::getActiveQueryWhere( $shop );
			
			$list = static::fetch(
				where_per_model: [ 'this'=>$where],
				order_by: 'priority',
				item_key_generator: function( Marketing_Gift_Product $item ) : int {
					return $item->getGiftProductId();
				}
			);
			
			foreach($list as $id=>$gift) {
				if(!$gift->isAvailable( $cart )) {
					unset($list[$id]);
				}
			}
			
			static::$available[$key] = $list;
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
}