<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


abstract class Core_Carrier_AdditionalConsignmentParameter
{
	protected string $code = '';
	protected string $name = '';
	protected string $description = '';
	
	public function __construct( string $code, string $name, string $description )
	{
		$this->code = $code;
		$this->name = $name;
		$this->description = $description;
	}

	public function getCode(): string
	{
		return $this->code;
	}
	
	public function setCode( string $code ): void
	{
		$this->code = $code;
	}

	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription( string $description ): void
	{
		$this->description = $description;
	}
	
	
	
}
