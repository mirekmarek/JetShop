<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


abstract class Core_Exports_ProductParams_Item {
	protected string $name;
	protected bool|string|array|int|float $value;
	protected string $units;
	
	public function __construct( string $name, bool|string|array|int|float $value, string $units )
	{
		$this->name = $name;
		$this->value = $value;
		$this->units = $units;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function getValue(): bool|string|array|int|float
	{
		return $this->value;
	}
	
	public function getUnits(): string
	{
		return $this->units;
	}
	
	
}