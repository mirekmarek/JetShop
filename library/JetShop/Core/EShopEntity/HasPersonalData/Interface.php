<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



interface Core_EShopEntity_HasPersonalData_Interface {
	
	public static function findAndDeletePersonalData( int $customer_id );
	
	public function deletePersonalData() : void;
	
}