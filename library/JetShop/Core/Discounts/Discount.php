<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Discounts;

abstract class Core_Discounts_Discount
{
	public const DISCOUNT_TYPE_PRODUCTS_PERCENTAGE = 'products_percentage';
	public const DISCOUNT_TYPE_PRODUCTS_AMOUNT = 'products_amount';
	
	public const DISCOUNT_TYPE_DELIVERY_PERCENTAGE = 'delivery_percentage';
	public const DISCOUNT_TYPE_DELIVERY_AMOUNT = 'delivery_amount';
	
	//public const DISCOUNT_TYPE_ORDER_PERCENTAGE = 'order_percentage';
	//public const DISCOUNT_TYPE_ORDER_AMOUNT = 'order_amount';
	
	
	protected string $discount_module = '';
	
	protected string $discount_context = '';
	
	protected string $discount_type = '';
	
	protected string $description = '';
	
	protected float $amount;
	
	protected float $vat_rate;
	
	public static function getDiscountTypeScope(): array
	{
		$dictionary = Discounts::Manager()->getModuleManifest()->getName();
		
		return [
			self::DISCOUNT_TYPE_PRODUCTS_PERCENTAGE => Tr::_( 'products - % discount', dictionary: $dictionary ),
			self::DISCOUNT_TYPE_PRODUCTS_AMOUNT     => Tr::_( 'products - amount', dictionary: $dictionary ),
			
			self::DISCOUNT_TYPE_DELIVERY_PERCENTAGE => Tr::_( 'delivery - % discount', dictionary: $dictionary ),
			self::DISCOUNT_TYPE_DELIVERY_AMOUNT     => Tr::_( 'delivery - amount', dictionary: $dictionary ),
			
			//self::DISCOUNT_TYPE_ORDER_PERCENTAGE => Tr::_( 'order - % discount', dictionary: $dictionary ),
			//self::DISCOUNT_TYPE_ORDER_AMOUNT     => Tr::_( 'order - amount', dictionary: $dictionary ),
		];
	}
	
	
	public function getKey() : string
	{
		return $this->discount_module.':'.$this->discount_context;
	}
	
	public function getDiscountModule(): string
	{
		return $this->discount_module;
	}
	
	public function setDiscountModule( string $discount_module ): void
	{
		$this->discount_module = $discount_module;
	}
	
	public function getDiscountContext(): string
	{
		return $this->discount_context;
	}
	
	public function setDiscountContext( string $discount_context ): void
	{
		$this->discount_context = $discount_context;
	}
	
	
	public function getDiscountType(): string
	{
		return $this->discount_type;
	}
	
	public function setDiscountType( string $discount_type ): void
	{
		$this->discount_type = $discount_type;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ): void
	{
		$this->description = $description;
	}
	
	public function getAmount(): float
	{
		return $this->amount;
	}
	
	public function setAmount( float $amount ): void
	{
		$this->amount = $amount;
	}
	
	public function getVatRate(): float
	{
		return $this->vat_rate;
	}
	
	public function setVatRate( float $vat_rate ): void
	{
		$this->vat_rate = $vat_rate;
	}
}