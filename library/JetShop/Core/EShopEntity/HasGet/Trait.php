<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


trait Core_EShopEntity_HasGet_Trait {
	
	protected static array $loaded_items = [];
	
	public static function get( int|string $id ) : ?static
	{
		$key = get_called_class().':'.$id;
		
		if(!array_key_exists($key, static::$loaded_items)) {
			$where['id'] = $id;
			
			static::$loaded_items[ $key ] = static::load( $where );
		}
		
		
		return static::$loaded_items[ $key ];
	}

}