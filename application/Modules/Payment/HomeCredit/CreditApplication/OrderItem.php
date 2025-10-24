<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\HomeCredit;

use JsonSerializable;

class CreditApplication_OrderItem implements JsonSerializable
{
	protected Config_PerShop $config;
	
	protected string $code = '5202';
	protected string $ean = '9999545';
	protected string $name = '';
	protected int $quantity = 1;
	protected float $amount = 0.0;
	protected string $image_filename = '';
	protected string $image_url = '';
	
	
	public function setConfig( Config_PerShop $config ) : void
	{
		$this->config = $config;
	}
	
	public function setCode( string $code ) : void
	{
		$this->code = $code;
	}
	
	public function setEan( string $ean ) : void
	{
		$this->ean = $ean;
	}
	
	public function setName( string $name ) : void
	{
		$this->name = $name;
	}
	
	public function setQuantity( int $quantity ) : void
	{
		$this->quantity = $quantity;
	}
	
	public function setAmount( float $amount ) : void
	{
		$this->amount = $amount*10;
	}
	
	public function getAmount() : float
	{
		return $this->amount;
	}
	
	public function getImageFilename() : string
	{
		return $this->image_filename;
	}
	
	public function getImageUrl() : string
	{
		return $this->image_url;
	}
	
	public function setImage( string $image_url, string $image_filename ) : void
	{
		$this->image_url = $image_url;
		$this->image_filename = $image_filename;
	}
	
	
	
	public function jsonSerialize() : array
	{
		return [
			'code' => $this->code,
			'ean' => $this->ean,
			'name' => $this->name,
			'quantity' => $this->quantity,
			'totalPrice' => [
				'amount' => round($this->amount*10),
				'currency' => $this->config->getEshop()->getDefaultPricelist()->getCurrency()->getCode(),
			],
			'image' => [
				'filename' => $this->image_filename,
				'url' => $this->image_url
			]
		];
	}
	
}