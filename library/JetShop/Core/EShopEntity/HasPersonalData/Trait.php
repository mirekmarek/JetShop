<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


trait Core_EShopEntity_HasPersonalData_Trait {
	
	public static function findAndDeletePersonalData( int $customer_id, string $customer_email, string $customer_phone_number ) : void
	{
		$items = static::fetch([''=>[
			'customer_id' => $customer_id,
		]]);
		
		foreach($items as $item) {
			$item->deletePersonalData();
		}
	}

}