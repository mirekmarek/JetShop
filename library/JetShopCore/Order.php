<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;


#[DataModel_Definition(
	name: 'order',
	database_table_name: 'orders',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Order extends DataModel {

	use Order_Traits_DataModel;
	use Order_Traits_OrderProcess;

	protected static ?Order $order_process_order = null;

	public static function getOrderProcess() : Order
	{
		if( !Order::$order_process_order ) {
			if( !isset( $_SESSION['order'] ) ) {
				$_SESSION['order'] = new Order();
			}
			Order::$order_process_order = $_SESSION['order'];

			Order::$order_process_order->orderProcess_init( ShoppingCart::get() );
		}

		return Order::$order_process_order;
	}

	public function orderProcess_init( ShoppingCart $cart ) : void
	{
		/**
		 * @var Order $this
		 */
		$this->items = [];

		$this->shop_id = $cart->getShopId();

		$this->orderProcess_init_items( $cart );

		//TODO: delivery
		//TODO: payment
		//TODO: discounts

		$this->recalculate();

		//TODO:
	}

	public function orderProcess_init_items( ShoppingCart $cart ) : void
	{
		/**
		 * @var Order $_this
		 */
		$_this =  $this;
		

		$this->items = $cart->getOrderItems();

		foreach( $this->items as $item ) {
			$item->setOrder( $_this );
		}

	}

	public function recalculate() : void
	{

		$this->total_price = 0.0;
		$this->product_price = 0.0;
		$this->service_price = 0.0;
		$this->delivery_price = 0.0;
		$this->payment_price = 0.0;

		$this->all_products_in_stocks = true;

		foreach( $this->items as $item ) {
			if(
				(
					$item->getType()==Order_Item::ITEM_TYPE_PRODUCT ||
					$item->getType()==Order_Item::ITEM_TYPE_GIFT
				)
				&&
				!$item->isInStock()
			) {
				$this->all_products_in_stocks = false;
			}

			if($item->getType()==Order_Item::ITEM_TYPE_DISCOUNT) {
				continue;
			}

			$price = $item->getTotalPrice();

			$this->total_price += $price;

			switch($item->getType()) {
				case Order_Item::ITEM_TYPE_PRODUCT:
				case Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT:
				case Order_Item::ITEM_TYPE_GIFT:
					$this->product_price += $price;
				break;
				case Order_Item::ITEM_TYPE_SERVICE:
					$this->service_price += $price;
				break;
				case Order_Item::ITEM_TYPE_PAYMENT:
					$this->payment_price += $price;
				break;
				case Order_Item::ITEM_TYPE_DELIVERY:
					$this->delivery_price += $price;
				break;

			}

		}

		$this->total_price_without_discount = $this->total_price;
		$this->product_price_without_discount = $this->product_price;
		$this->service_price_without_discount = $this->service_price;
		$this->delivery_price_without_discount = $this->delivery_price;
		$this->payment_price_without_discount = $this->payment_price;

		$this->discount = 0.0;
		$this->discount_percentage = 0.0;

		foreach( $this->items as $item ) {
			if($item->getType()!=Order_Item::ITEM_TYPE_DISCOUNT) {
				continue;
			}

			$discount = abs($item->getTotalPrice());
			$discount_updated = false;

			switch($item->getSubType()) {
				case Order_Item::DISCOUNT_TYPE_PRODUCTS:
					if($discount>$this->product_price) {
						$discount = $this->product_price;
						$discount_updated = true;
					}

					$this->product_price -= $discount;
				break;
				case Order_Item::DISCOUNT_TYPE_SERVICE:
					if($discount>$this->service_price) {
						$discount = $this->service_price;
						$discount_updated = true;
					}

					$this->service_price -= $discount;
				break;
				case Order_Item::DISCOUNT_TYPE_DELIVERY:
					if($discount>$this->delivery_price) {
						$discount = $this->delivery_price;
						$discount_updated = true;
					}

					$this->delivery_price -= $discount;
				break;
				case Order_Item::DISCOUNT_TYPE_PAYMENT:
					if($discount>$this->payment_price) {
						$discount = $this->payment_price;
						$discount_updated = true;
					}

					$this->payment_price -= $discount;

				break;
			}

			if($discount>$this->total_price) {
				$discount = $this->total_price;
				$discount_updated = true;
			}

			if($discount_updated) {
				$item->setPricePerItem( $discount );
			}

			$this->discount += $discount;
			$this->total_price -= $discount;
		}


		if($this->discount) {
			$this->discount_percentage = (1-$this->total_price / $this->total_price_without_discount)*100;
			$this->discount_percentage = round($this->discount_percentage, 3);
		}

	}


}