<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_VirtualStatus;

interface Core_EShopEntity_HasStatus_Interface {
	
	public function getFlags() : array;
	
	public function setFlags( array $flags ) : void;
	
	public function setStatus( EShopEntity_Status|EShopEntity_VirtualStatus $status, bool $handle_event=true ) : void;
	
	public function getStatus() : ?EShopEntity_Status;
	
	public function getStatusCode() : string;
	
	public function setStatusByFlagState( bool $set=true ) : ?EShopEntity_Status;
	
	/**
	 * @return EShopEntity_Status[]
	 */
	public static function getStatusList(): array;
}