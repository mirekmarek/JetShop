<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


interface Core_EShopEntity_HasInternalParams_Interface {
	
	public function getInternalName(): string;
	public function setInternalName( string $internal_name ): void;
	
	public function getInternalCode(): string;
	public function setInternalCode( string $internal_code ): void;
	
	public function getInternalNotes(): string;
	public function setInternalNotes( string $internal_notes ): void;
	
	public static function internalCodeUsed( string $internal_code, int $skip_id=0 ) : bool;
	
	public static function getScope() : array;
	public static function getOptionsScope() : array;
	
	public function getAdminTitle() : string;
}