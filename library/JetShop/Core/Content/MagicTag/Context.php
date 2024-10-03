<?php
namespace JetShop;


class Core_Content_MagicTag_Context {
	public const TYPE_PAGE = 'page';
	public const TYPE_PRODUCT = 'product';
	public const TYPE_CATEGORY = 'category';
	public const TYPE_STRING = 'string';
	public const TYPE_INT = 'int';
	public const TYPE_FLOAT = 'float';
	public const TYPE_BOOL = 'bool';
	
	protected string $name = '';
	protected string $type = '';
	protected string $description = '';
	
	public function __construct( string $name, string $type, string $description )
	{
		$this->name = $name;
		$this->type = $type;
		$this->description = $description;
	}
	
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getType(): string
	{
		return $this->type;
	}
	
	public function setType( string $type ): void
	{
		$this->type = $type;
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