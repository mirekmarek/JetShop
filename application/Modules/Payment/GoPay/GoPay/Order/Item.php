<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\GoPay;


class GoPay_Order_Item
{
	protected string $name = '';
	protected int $count = 0;
	protected float $amount = 0.0;

	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}

	public function getCount(): int
	{
		return $this->count;
	}
	
	public function setCount( int $count ): void
	{
		$this->count = $count;
	}
	
	public function getAmount(): float
	{
		return $this->amount;
	}
	
	public function setAmount( float $amount ): void
	{
		$this->amount = $amount;
	}
	
	
	
}