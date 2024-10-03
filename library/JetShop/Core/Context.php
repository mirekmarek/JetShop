<?php
namespace JetShop;


abstract class Core_Context {
	
	public const COMPLAINT = 'complaint';
	
	protected string $context_type = '';
	protected int $context_id = 0;
	protected string $context_number = '';
	
	
	
	public function __construct( string $context_type, int $context_id, string $context_number='' )
	{
		$this->context_type = $context_type;
		$this->context_id = $context_id;
		$this->context_number = $context_number;
	}
	
	public function getContextType(): string
	{
		return $this->context_type;
	}
	
	public function getContextId(): int
	{
		return $this->context_id;
	}
	
	public function getContextNumber(): string
	{
		return $this->context_number;
	}
	
	public function getWhere() : array
	{
		return [
			'context_type' => $this->context_type,
			'AND',
			'context_id' => $this->context_id,
		];
	}
}