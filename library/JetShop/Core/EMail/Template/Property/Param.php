<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Exception;

abstract class Core_EMail_Template_Property_Param {
	
	public const TYPE_INT = 'int';
	public const TYPE_FLOAT = 'float';
	public const TYPE_STRING = 'string';
	
	protected string $type;
	
	protected string $name;
	
	protected string $description = '';
	
	public function getType(): string
	{
		return $this->type;
	}
	
	public function setType( string $type ): void
	{
		$this->type = $type;
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
	
	public function createRegExp() : string
	{
		return match ($this->type) {
			static::TYPE_INT => '([0-9]{1,})',
			static::TYPE_FLOAT => '([0-9]{1,}\.[0-9]{1,})',
			static::TYPE_STRING => '("[0-9A-Za-z \']{1,}")',
			default => throw new Exception( 'Unknown property type ' . $this->type ),
		};
	}
	
	public function getDefaultValue() : string|int|float
	{
		return match ($this->type) {
			static::TYPE_INT => 0,
			static::TYPE_FLOAT => 0.0,
			static::TYPE_STRING => '',
			default => throw new Exception( 'Unknown property type ' . $this->type ),
		};
	}
	
	public function checkType( string $parsed_value ) : string|int|float
	{
		return match ($this->type) {
			static::TYPE_INT => (int)$parsed_value,
			static::TYPE_FLOAT => (float)$parsed_value,
			static::TYPE_STRING => $parsed_value,
			default => throw new Exception( 'Unknown property type ' . $this->type ),
		};
	}
	
	public function getInstructionTemplate() : string
	{
		return match ($this->type) {
			static::TYPE_INT => 'int '.$this->name,
			static::TYPE_FLOAT => 'float '.$this->name,
			static::TYPE_STRING => "string '{$this->name}'",
			default => throw new Exception( 'Unknown property type ' . $this->type ),
		};
		
	}

}