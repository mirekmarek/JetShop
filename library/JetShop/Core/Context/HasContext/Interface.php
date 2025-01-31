<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Context;

interface Core_Context_HasContext_Interface
{
	public function setContext( Context $context ) : void;
	
	public function getContext() : Context;
	
	public function getContextType(): string;
	
	public function setContextType( string $context_type ): void;
	
	public function getContextId(): int;
	
	public function setContextId( int $context_id ): void;
	
	public function getContextNumber(): string;
	
	public function setContextNumber( string $context_number ): void;

}