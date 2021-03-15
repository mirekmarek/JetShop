<?php
/**
 *
 */
namespace JetShop;

abstract class Core_Delivery_Pricing_PriceInfo {

	protected Delivery_Method $delivery_method;

	protected float $standard_price = 0.0;

	protected float $vat_rate = 0.0;

	protected float $final_price = 0.0;

	protected bool $is_promo_price = false;

	protected string $promotion_code = '';

	protected string $promotion_description = '';

	public function __construct( Delivery_Method $delivery_method )
	{
		$this->delivery_method = $delivery_method;
	}

	public function getDeliveryMethod(): Delivery_Method
	{
		return $this->delivery_method;
	}

	public function getStandardPrice(): float
	{
		return $this->standard_price;
	}

	public function setStandardPrice( float $standard_price ): void
	{
		$this->standard_price = $standard_price;
	}

	public function getVatRate(): float
	{
		return $this->vat_rate;
	}

	public function setVatRate( float $vat_rate ): void
	{
		$this->vat_rate = $vat_rate;
	}

	public function getFinalPrice(): float
	{
		return $this->final_price;
	}

	public function setFinalPrice( float $final_price ): void
	{
		$this->final_price = $final_price;
	}

	public function isIsPromoPrice(): bool
	{
		return $this->is_promo_price;
	}

	public function setIsPromoPrice( bool $is_promo_price ): void
	{
		$this->is_promo_price = $is_promo_price;
	}

	public function getPromotionCode(): string
	{
		return $this->promotion_code;
	}

	public function setPromotionCode( string $promotion_code ): void
	{
		$this->promotion_code = $promotion_code;
	}

	public function getPromotionDescription(): string
	{
		return $this->promotion_description;
	}

	public function setPromotionDescription( string $promotion_description ): void
	{
		$this->promotion_description = $promotion_description;
	}



}