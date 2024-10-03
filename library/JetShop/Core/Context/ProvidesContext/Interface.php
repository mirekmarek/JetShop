<?php
namespace JetShop;

use JetApplication\Context;

interface Core_Context_ProvidesContext_Interface
{
	public static function getProvidesContextType() : string;
	
	public function getProvidesContextId(): int;
	
	public function getProvidesContextNumber(): string;
	
	public function getProvidesContext() : Context;
}