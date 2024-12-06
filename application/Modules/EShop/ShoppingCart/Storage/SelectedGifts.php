<?php
/**
 *
 */

namespace JetApplicationModule\EShop\ShoppingCart;

use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Locale;
use JetApplication\ShoppingCart;
use JetApplication\EShop;
use Jet\Data_DateTime;


#[DataModel_Definition(
	name: 'shopping_cart_selected_gifts',
	database_table_name: 'shopping_cart_selected_gifts',
	id_controller_class: DataModel_IDController_Passive::class
)]
class Storage_SelectedGifts extends DataModel
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true
	)]
	protected string $id = '';
	
	protected ?EShop $_eshop = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_id: true,
		is_key: true,
		max_len: 100
	)]
	protected string $eshop_code = '';
	
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
		is_key: true
	)]
	protected int $selected_gift_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $customer_id = 0;
	
	public function setEshop( EShop $eshop ): void
	{
		$this->eshop_code = $eshop->getCode();
		$this->locale = $eshop->getLocale();
		$this->_eshop = $eshop;
	}
	
	/**
	 * @param string $id
	 * @return static[]
	 */
	public static function get( string $id ): array
	{
		return static::fetch( ['this' => ['id' => $id]] );
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
		foreach( $cart->getSelectedCartGiftIds() as $gift_id ) {
			$storage = new static();
			$storage->setEshop( $cart->getEshop() );
			$storage->setId( $cart->getId() );
			$storage->setSelectedGiftId( $gift_id );
			if( ($user = Auth::getCurrentUser()) ) {
				$storage->setCustomerId( $user->getId() );
			}
			$storage->setLastActivityDateTime( $now );
			$storage->save();
		}
		
	}
	
	public static function loadCart( ShoppingCart $cart ): void
	{
		$selected_gift_ids = [];
		
		foreach(static::get( $cart->getId() ) as $item) {
			$selected_gift_ids[] = $item->getSelectedGiftId();
		}
		
		$cart->setSelectedCartGifts( $selected_gift_ids );
		
	}
}