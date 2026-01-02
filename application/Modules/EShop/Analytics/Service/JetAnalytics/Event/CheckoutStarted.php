<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Locale;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\Currencies;
use JetApplication\Delivery_Method;
use JetApplication\MeasureUnits;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Payment_Method;
use JetApplication\Pricelists;

#[DataModel_Definition(
	name: 'ja_event_checkout_started',
	database_table_name: 'ja_event_checkout_started',
)]
class Event_CheckoutStarted extends Event
{
	
	/**
	 * @var Event_CheckoutStarted_Item[]
	 */
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
		return true;
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
		
		$this->initItems( $order );
	}
	
	protected function initItems( Order $order ) : void
	{
		foreach($order->getItems() as $item) {
			foreach(Event_CheckoutStarted_Item::createNew( $this, $item ) as $created_item) {
				$this->items[] = $created_item;
			}
		}
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Checkout started');
	}
	
	public function getCssClass(): string
	{
		return 'info';
	}
	
	
	public function showShortDetails(): string
	{
		$res = '';
		
		
		$price_formatter = Application_Service_Admin::PriceFormatter();
		
		$res = '<table>';
		
		foreach($this->items as $item) {
			$unit = MeasureUnits::get( $item->getMeasureUnit() );
			$currency = Currencies::get( $item->getCurrencyCode() );
			$pricelist = Pricelists::get( $item->getPricelistCode() );
			
			$res .= '<tr>';
			
			if(!$unit) {
				$res .= '<td>'.$item->getNumberOfUnits().'</td>';
			} else {
				$res .= '<td>'.$unit->round($item->getNumberOfUnits()).' '.$unit->getNAme(Locale::getCurrentLocale()).'</td>';
			}
			
			$res .= '<td>';
			switch($item->getType()) {
				case Order_Item::ITEM_TYPE_PRODUCT:
				case Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT:
				case Order_Item::ITEM_TYPE_GIFT:
				case Order_Item::ITEM_TYPE_VIRTUAL_GIFT:
						$res .= Application_Service_Admin::Product()->renderItemName( $item->getItemId() );
					break;
				case Order_Item::ITEM_TYPE_DELIVERY:
						$res .= Delivery_Method::getScope()[$item->getItemId()]??'';
					break;
				case Order_Item::ITEM_TYPE_PAYMENT:
						$res .= Payment_Method::getScope()[$item->getItemId()]??'';
					break;
				case Order_Item::ITEM_TYPE_SERVICE:
				case Order_Item::ITEM_TYPE_DISCOUNT:
						$res .= $item->getItemCode().':'.$item->getSubCode();
					break;
			}
			$res .= '</td>';
			
			
			$res .= '<td>'.$price_formatter->formatWithCurrency( $pricelist, $item->getPricePerUnit() ).'</td>';
			$res .= '<td>'.$price_formatter->formatWithCurrency( $pricelist, $item->getPricePerUnit()*$item->getNumberOfUnits() ).'</td>';
			$res .= '</tr>';
		}
		
		$res .= '</table>';
		
		return $res;
	}
	
	public function getIcon(): string
	{
		return 'cash-register';
	}
	
	public function showLongDetails(): string
	{
		//TODO:
		return '';
	}
	
	public function getItems(): array
	{
		return $this->items;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function getPricelistCode(): string
	{
		return $this->pricelist_code;
	}
	
	public function getPaymentMethodId(): float
	{
		return $this->payment_method_id;
	}
	
	public function getDeliveryMethodId(): float
	{
		return $this->delivery_method_id;
	}
	
	public function getProductAmountWithVAT(): float
	{
		return $this->product_amount_with_VAT;
	}
	
	public function getProductAmountWithoutVAT(): float
	{
		return $this->product_amount_without_VAT;
	}
	
	public function getDeliveryAmountWithVAT(): float
	{
		return $this->delivery_amount_with_VAT;
	}
	
	public function getDeliveryAmountWithoutVAT(): float
	{
		return $this->delivery_amount_without_VAT;
	}
	
	public function getPaymentAmountWithVAT(): float
	{
		return $this->payment_amount_with_VAT;
	}
	
	public function getPaymentAmountWithoutVAT(): float
	{
		return $this->payment_amount_without_VAT;
	}
	
	public function getServiceAmountWithVAT(): float
	{
		return $this->service_amount_with_VAT;
	}
	
	public function getServiceAmountWithoutVAT(): float
	{
		return $this->service_amount_without_VAT;
	}
	
	public function getDiscountAmountWithVAT(): float
	{
		return $this->discount_amount_with_VAT;
	}
	
	public function getDiscountAmountWithoutVAT(): float
	{
		return $this->discount_amount_without_VAT;
	}
	
	public function getTotalAmountWithVAT(): float
	{
		return $this->total_amount_with_VAT;
	}
	
	public function getTotalAmountWithoutVAT(): float
	{
		return $this->total_amount_without_VAT;
	}
	
	
}