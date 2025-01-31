<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Context;

trait Core_Context_ProvidesContext_Trait {
	
	public static function getProvidesContextType() : string
	{
		return static::getEntityType();
	}
	
	public function getProvidesContextId(): int
	{
		return $this->getId();
	}
	
	public function getProvidesContextNumber(): string
	{
		return $this->getNumber();
	}
	
	public function getProvidesContext() : Context
	{
		return new Context(
			context_type: static::getProvidesContextType(),
			context_id: $this->getProvidesContextId(),
			context_number: $this->getProvidesContextNumber()
		);
	}
}