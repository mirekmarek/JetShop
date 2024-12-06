<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Marketing_Gift_Product;
use JetApplication\MeasureUnit;
use JetApplication\Pricelists;
use JetApplication\Product_EShopData;
use JetApplication\ShoppingCart;

abstract class Core_ShoppingCart_Item
{

	protected ?ShoppingCart $__cart = null;

	protected int $product_id = 0;

	protected float $number_of_units = 0.0;
	
	protected ?MeasureUnit $measure_unit = null;
	
	protected int $selected_gift_id = 0;
	
	protected int $auto_offer_id = 0;

	protected string $check_error_message = '';
	
	protected ?Product_EShopData $product=null;

	public function __construct( int $product_id, float $number_of_units, ?MeasureUnit $measure_unit=null, int $selected_gift_id=0 )
	{
		$this->product_id = $product_id;
		$this->selected_gift_id = $selected_gift_id;
		$this->measure_unit = $measure_unit;
		$this->number_of_units = $number_of_units;
		
		if($measure_unit) {
			$this->number_of_units = $measure_unit->round( $number_of_units );
		} else {
			$this->number_of_units = $number_of_units;
		}
	}

	public function getCart() : ShoppingCart
	{
		return $this->__cart;
	}

	public function setCart( ShoppingCart $cart ) : void
	{
		$this->__cart = $cart;
	}
	

	public function getAutoOfferId(): int
	{
		return $this->auto_offer_id;
	}
	
	public function setAutoOfferId( int $auto_offer_id ): void
	{
		$this->auto_offer_id = $auto_offer_id;
	}
	
	

	public function getProductId() : int
	{
		return $this->product_id;
	}

	public function getProduct() : ?Product_EShopData
	{
		if($this->product===null) {
			$this->product = Product_EShopData::get( $this->product_id, $this->getCart()->getEshop() );
		}
		
		return $this->product;
	}

	public function getNumberOfUnits() : float
	{
		return $this->number_of_units;
	}
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return $this->measure_unit;
	}
	
	

	public function isValid() : bool
	{
		if(
			!$this->number_of_units ||
			!$this->product_id ||
			!$this->getProduct() ||
			!$this->getProduct()->isActive() ||
			!$this->getProduct()->getPrice( $this->getCart()->getPricelist() )
		) {
			return false;
		}
		
		if($this->selected_gift_id) {
			$gifts = Marketing_Gift_Product::getAvailable( $this->getCart() );
			if(
				!isset($gifts[$this->selected_gift_id]) ||
				!$gifts[$this->selected_gift_id]->productIsRelevant( $this->getCart(), $this->getProduct() )
			) {
				$this->selected_gift_id = 0;
			}
		}
		
		return true;
	}

	public function setNumberOfUnits( float $number_of_units ) : bool
	{
		if($this->measure_unit) {
			$number_of_units = $this->measure_unit->round( $number_of_units );
		}
		
		if(!$this->checkQuantity($number_of_units)) {
			if($number_of_units<=0) {
				return false;
			}
		}

		$this->number_of_units = $number_of_units;

		return true;
	}
	
	public function getSelectedGiftId(): int
	{
		return $this->selected_gift_id;
	}
	
	public function getSelectedGift(): ?Marketing_Gift_Product
	{
		if(!$this->selected_gift_id) {
			return null;
		}
		
		return Marketing_Gift_Product::getAvailable( $this->getCart() )[$this->selected_gift_id];
	}

	public function getAmount() : float|int
	{
		return $this->number_of_units * $this->getProduct()->getPrice( Pricelists::getCurrent() );
	}

	public function getCheckErrorMessage() : string
	{
		return $this->check_error_message;
	}

	public function setCheckErrorMessage( string $check_error_message ) : void
	{
		$this->check_error_message = $check_error_message;
	}
	
	public function checkQuantity( float &$number_of_units ) : bool
	{
		
		$p = $this->getProduct();
		$delivery_info = $p->getDeliveryInfo( $this->getCart()->getAvailability() );
		
		
		
		if(
			$delivery_info->getIsVirtualProduct() ||
			$number_of_units<=$delivery_info->getNumberOfUnitsAvailable() ||
			$delivery_info->getAllowToOrderMore()
		) {
			//var_dump( $number_of_units, $delivery_info->getNumberOfUnitsAvailable() );
			//die('???');
			return true;
		}
		
		$number_of_units = $delivery_info->getNumberOfUnitsAvailable();
		
		$this->check_error_message = Tr::_(
			text: 'Sorry, but the maximum quantity is %units% %mu%',
			data: [
				'units' => $number_of_units,
				'mu' => $p->getKind()->getMeasureUnit()->getName( $this->getCart()->getEshop()->getLocale() )
			],
			locale: $this->getCart()->getEshop()->getLocale()
		);
		
		return true;
	}

}