<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Context;

interface Core_Context_ProvidesContext_Interface
{
	public static function getProvidesContextType() : string;
	
	public function getProvidesContextId(): int;
	
	public function getProvidesContextNumber(): string;
	
	public function getProvidesContext() : Context;
}