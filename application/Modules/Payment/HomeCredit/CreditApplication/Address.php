<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\HomeCredit;

use JsonSerializable;

class CreditApplication_Address implements JsonSerializable
{
	protected string $city = '';
	protected string $streetAddress = '';
	protected string $streetNumber = '';
	protected string $zip = '';
	protected string $addressType = 'PERMANENT';
	
	public function setCity( string $city ) : void
	{
		$this->city = $city;
	}
	
	public function setStreetAddress( string $streetAddress ) : void
	{
		$this->streetAddress = $streetAddress;
	}
	
	public function setStreetNumber( string $streetNumber ) : void
	{
		$this->streetNumber = $streetNumber;
	}
	
	public function setZip( string $zip ) : void
	{
		$this->zip = $zip;
	}
	
	public function setAddressType( string $addressType ) : void
	{
		$this->addressType = $addressType;
	}
	
	public function jsonSerialize() : array
	{
		return [
			"city"          => $this->city,
			"streetAddress" => $this->streetAddress,
			"streetNumber"  => $this->streetNumber,
			"zip"           => $this->zip,
			"addressType"   => $this->addressType,
			
		];
	}
	
}