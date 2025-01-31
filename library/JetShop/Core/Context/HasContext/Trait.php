<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Context;

trait Core_Context_HasContext_Trait
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50
	)]
	protected string $context_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $context_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50
	)]
	protected string $context_number = '';
	
	public function setContext( Context $context ) : void
	{
		$this->context_type = $context->getContextType();
		$this->context_id = $context->getContextId();
		$this->context_number = $context->getContextNumber();
	}
	
	public function getContext() : Context
	{
		return new Context(
			context_type: $this->context_type,
			context_id: $this->context_id,
			context_number: $this->context_number
		);
	}
	
	
	public function getContextType(): string
	{
		return $this->context_type;
	}
	
	public function setContextType( string $context_type ): void
	{
		$this->context_type = $context_type;
	}
	
	public function getContextId(): int
	{
		return $this->context_id;
	}
	
	public function setContextId( int $context_id ): void
	{
		$this->context_id = $context_id;
	}
	
	public function getContextNumber(): string
	{
		return $this->context_number;
	}
	
	public function setContextNumber( string $context_number ): void
	{
		$this->context_number = $context_number;
	}
	
	
}