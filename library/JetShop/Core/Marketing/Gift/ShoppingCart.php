<?php
namespace JetShop;

use Jet\DataModel;

use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_Marketing;
use JetApplication\Marketing_Gift;
use JetApplication\Marketing_Gift_ShoppingCart;
use JetApplication\Product_EShopData;
use JetApplication\ShoppingCart;

#[DataModel_Definition(
	name: 'gift_shipping_cart',
	database_table_name: 'gifts_shipping_cart',
)]
abstract class Core_Marketing_Gift_ShoppingCart extends Entity_Marketing
{
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
		label: 'Order amount limit - min:',
	)]
	protected float $order_amount_limit_min = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Order amount limit - max:',
	)]
	protected float $order_amount_limit_max = 0.0;
	
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
		label: 'Auto append',
	)]
	protected bool $auto_append = false;

	
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

	public function getOrderAmountLimitMin(): float
	{
		return $this->order_amount_limit_min;
	}
	
	public function setOrderAmountLimitMin( float $order_amount_limit_min ): void
	{
		$this->order_amount_limit_min = $order_amount_limit_min;
	}
	
	public function getOrderAmountLimitMax(): float
	{
		return $this->order_amount_limit_max;
	}
	
	public function setOrderAmountLimitMax( float $order_amount_limit_max ): void
	{
		$this->order_amount_limit_max = $order_amount_limit_max;
	}

	public function getInStockLimit(): int
	{
		return $this->in_stock_limit;
	}
	
	public function setInStockLimit( int $in_stock_limit ): void
	{
		$this->in_stock_limit = $in_stock_limit;
	}
	
	public function getAutoAppend(): bool
	{
		return $this->auto_append;
	}
	
	public function setAutoAppend( bool $auto_append ): void
	{
		$this->auto_append = $auto_append;
	}
	
	public function isRelevantForCart( ShoppingCart $cart ) : bool
	{
		if(
			!$this->order_amount_limit_min &&
			!$this->order_amount_limit_max
		) {
			return true;
		}
		
		$amount = $cart->getAmount();
		
		if(
			$this->order_amount_limit_min &&
			$amount<$this->order_amount_limit_min
		) {
			return false;
		}
		
		if(
			$this->order_amount_limit_max &&
			$amount>$this->order_amount_limit_max
		) {
			return false;
		}
		
		
		return true;
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
	
	public function cartIsRelevant( ShoppingCart $cart ): bool
	{
		$amount = $cart->getAmount();
		
		if(
			$this->order_amount_limit_min==0 &&
			$this->order_amount_limit_max==0
		) {
			return true;
		}
		
		if(
			$this->order_amount_limit_min>0 &&
			$amount<$this->order_amount_limit_min
		) {
			return false;
		}
		
		if(
			$this->order_amount_limit_max>0 &&
			$amount>$this->order_amount_limit_max
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
		$eshop = $cart->getEshop();
		
		$key = $eshop->getKey().':'.$cart->getAvailability()->getCode().':'.$cart->getPricelist()->getCode();
		
		if(!isset( static::$available[$key])) {
			$where = static::getActiveQueryWhere( $eshop );
			
			$list = static::fetch(
				where_per_model: [ 'this'=>$where],
				order_by: 'priority',
				item_key_generator: function( Marketing_Gift_ShoppingCart $item ) : int {
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
		$gift->setAutoAppend( $this->getAutoAppend() );
		$gift->setInStockLimit( $this->getInStockLimit() );
		
		return $gift;
	}
	
}