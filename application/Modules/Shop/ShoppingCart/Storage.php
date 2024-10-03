<?php
/**
 * 
 */

namespace JetApplicationModule\Shop\ShoppingCart;

use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Locale;
use JetApplication\MeasureUnit;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Shops_Shop;
use Jet\Data_DateTime;


#[DataModel_Definition(
	name: 'shopping_cart',
	database_table_name: 'shopping_cart',
	id_controller_class: DataModel_IDController_Passive::class
)]
class Storage extends DataModel
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true
	)]
	protected string $id = '';
	
	protected ?Shops_Shop $_shop = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_id: true,
		is_key: true,
		max_len: 100
	)]
	protected string $shop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
	)]
	protected ?Locale $locale = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $last_activity_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		is_key: true
	)]
	protected float $number_of_units = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 64
	)]
	protected string $measure_unit = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $auto_offer_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $selected_gift_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $customer_id = 0;
	
	public function setShop( Shops_Shop $shop ): void
	{
		$this->shop_code = $shop->getShopCode();
		$this->locale = $shop->getLocale();
		$this->_shop = $shop;
	}
	
	/**
	 * @param string $id
	 * @return static[]
	 */
	public static function get( string $id ): array
	{
		return static::fetch( ['shopping_cart' => ['id' => $id]] );
	}
	
	
	public function setId( string $value ): void
	{
		$this->id = $value;
	}
	
	
	public function getId(): string
	{
		return $this->id;
	}
	
	public function setLastActivityDateTime( Data_DateTime|string|null $value ): void
	{
		$this->last_activity_date_time = Data_DateTime::catchDateTime( $value );
	}
	
	public function getLastActivityDateTime(): Data_DateTime|null
	{
		return $this->last_activity_date_time;
	}
	
	public function setProductId( int $value ): void
	{
		$this->product_id = $value;
	}

	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setNumberOfUnits( float $number_of_units, ?MeasureUnit $measure_unit ): void
	{
		if($measure_unit) {
			$number_of_units = $measure_unit->round($number_of_units);
			$this->measure_unit = $measure_unit->getCode();
		}
		
		$this->number_of_units = $number_of_units;
	}
	
	public function getNumberOfUnits(): float
	{
		return $this->number_of_units;
	}
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnit::get( $this->measure_unit );
	}
	
	
	public function getAutoOfferId(): int
	{
		return $this->auto_offer_id;
	}
	
	public function setAutoOfferId( int $auto_offer_id ): void
	{
		$this->auto_offer_id = $auto_offer_id;
	}
	
	

	public function getSelectedGiftId(): int
	{
		return $this->selected_gift_id;
	}
	
	public function setSelectedGiftId( int $selected_gift_id ): void
	{
		$this->selected_gift_id = $selected_gift_id;
	}
	
	
	
	public function setCustomerId( int $value ): void
	{
		$this->customer_id = $value;
	}
	
	public function getCustomerId(): int
	{
		return $this->customer_id;
	}
	
	public static function saveCart( ShoppingCart $cart ): void
	{
		static::dataDelete( [
			'id' => $cart->getId()
		] );
		
		$now = Data_DateTime::now();
		foreach( $cart->getItems() as $item ) {
			$storage = new static();
			$storage->setShop( $cart->getShop() );
			$storage->setId( $cart->getId() );
			
			$storage->setProductId( $item->getProductId() );
			$storage->setNumberOfUnits( $item->getNumberOfUnits(), $item->getMeasureUnit() );
			$storage->setAutoOfferId( $item->getAutoOfferId() );
			$storage->setSelectedGiftId( $item->getSelectedGiftId() );
			if( ($user = Auth::getCurrentUser()) ) {
				$storage->setCustomerId( $user->getId() );
			}
			$storage->setLastActivityDateTime( $now );
			$storage->save();
		}
		
		Storage_SelectedGifts::saveCart( $cart );
	}
	
	public static function loadCart( ShoppingCart $cart ): void
	{
		$_items = static::get( $cart->getId() );
		$items = [];
		foreach($_items as $_item) {
			$item = new ShoppingCart_Item(
				$_item->getProductId(),
				$_item->getNumberOfUnits(),
				$_item->getMeasureUnit(),
				$_item->getSelectedGiftId()
			);
			
			$item->setAutoOfferId( $_item->getAutoOfferId() );
			
			$items[] = $item;
			
		}
		$cart->load( $items );
		
		Storage_SelectedGifts::loadCart( $cart );
		
	}
}