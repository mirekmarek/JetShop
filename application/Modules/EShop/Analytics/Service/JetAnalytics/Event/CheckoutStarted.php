<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Order;
use JetApplication\Pricelists;

#[DataModel_Definition(
	name: 'ja_event_checkout_started',
	database_table_name: 'ja_event_checkout_started',
)]
class Event_CheckoutStarted extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Event_CheckoutStarted_Item::class
	)]
	protected array $items = [];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $pricelist_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected float $payment_method_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected float $delivery_method_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $product_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $product_amount_without_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $delivery_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $delivery_amount_without_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $payment_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $payment_amount_without_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $service_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $service_amount_without_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $discount_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $discount_amount_without_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount_without_VAT = 0.0;
	
	
	public function cancelDefaultEvent(): bool
	{
		return false;
	}
	
	public function init( Order $order ) : void
	{
		$pricelist = Pricelists::getCurrent();
		
		$this->delivery_method_id= $order->getDeliveryMethodId();
		$this->payment_method_id= $order->getPaymentMethodId();
		$this->currency_code = $pricelist->getCurrencyCode();
		$this->pricelist_code = $pricelist->getCode();
		
		$this->product_amount_with_VAT = $order->getProductAmount_WithVAT();
		$this->product_amount_without_VAT = $order->getProductAmount_WithoutVAT();
		$this->delivery_amount_with_VAT = $order->getDeliveryAmount_WithVAT();
		$this->delivery_amount_without_VAT = $order->getDeliveryAmount_WithoutVAT();
		$this->payment_amount_with_VAT = $order->getPaymentAmount_WithVAT();
		$this->payment_amount_without_VAT = $order->getPaymentAmount_WithoutVAT();
		$this->discount_amount_with_VAT = $order->getDiscountAmount_WithVAT();
		$this->discount_amount_without_VAT = $order->getDiscountAmount_WithoutVAT();
		$this->total_amount_with_VAT = $order->getTotalAmount_WithVAT();
		$this->total_amount_without_VAT = $order->getTotalAmount_WithoutVAT();
		
	}
	
	protected function initItems( Order $order ) : void
	{
		foreach($order->getItems() as $item) {
			$this->items[] = Event_CheckoutStarted_Item::createNew( $this, $item );
		}
	}
}