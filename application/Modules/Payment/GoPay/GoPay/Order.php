<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\GoPay;


class GoPay_Order
{
	protected string $oder_number;
	protected string $description;
	protected string $first_name;
	protected string $last_name;
	protected string $email;
	protected string $phone_number;
	protected string $city;
	protected string $street;
	protected string $postal_code;
	protected string $country_code;
	protected float $amount = 0.0;
	protected string $currency;
	protected string $language;
	
	/**
	 * @var GoPay_Order_Item[]
	 */
	protected array $items = [];
	

	public function getOderNumber(): string
	{
		return $this->oder_number;
	}

	public function setOderNumber( string $oder_number ): void
	{
		$this->oder_number = $oder_number;
	}

	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ): void
	{
		$this->description = $description;
	}
	
	public function getFirstName(): string
	{
		return $this->first_name;
	}
	
	public function setFirstName( string $first_name ): void
	{
		$this->first_name = $first_name;
	}
	
	public function getLastName(): string
	{
		return $this->last_name;
	}
	
	public function setLastName( string $last_name ): void
	{
		$this->last_name = $last_name;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail( string $email ): void
	{
		$this->email = $email;
	}

	public function getPhoneNumber(): string
	{
		return $this->phone_number;
	}

	public function setPhoneNumber( string $phone_number ): void
	{
		$this->phone_number = $phone_number;
	}

	public function getCity(): string
	{
		return $this->city;
	}

	public function setCity( string $city ): void
	{
		$this->city = $city;
	}

	public function getStreet(): string
	{
		return $this->street;
	}

	public function setStreet( string $street ): void
	{
		$this->street = $street;
	}

	public function getPostalCode(): string
	{
		return $this->postal_code;
	}

	public function setPostalCode( string $postal_code ): void
	{
		$this->postal_code = $postal_code;
	}

	public function getCountryCode(): string
	{
		return $this->country_code;
	}

	public function setCountryCode( string $country_code ): void
	{
		$this->country_code = $country_code;
	}
	

	public function setAmount( float $amount ): void
	{
		$this->amount = $amount;
	}
	
	public function getAmount(): float
	{
		return $this->amount;
	}
	
	public function getCurrency(): string
	{
		return $this->currency;
	}
	
	public function setCurrency( string $currency ): void
	{
		$this->currency = $currency;
	}
	
	public function getLanguage(): string
	{
		return $this->language;
	}
	
	public function setLanguage( string $language ): void
	{
		$this->language = $language;
	}
	
	
	
	public function addItem( GoPay_Order_Item $item ) : void
	{
		$this->items[] = $item;
	}
	
	/**
	 * @return GoPay_Order_Item[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}
	
	
}