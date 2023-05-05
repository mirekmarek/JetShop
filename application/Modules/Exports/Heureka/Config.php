<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Exports\Heureka;

use Jet\BaseObject;
use Jet\Exception;
use JetApplication\Shops_Shop;

class Config extends BaseObject {

	public static function getCategoriesURL( Shops_Shop $shop ) : string
	{
		switch($shop->getLocale()->toString()) {
			case 'cs_CZ':
				return 'https://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml';
			case 'sk_SK':
				return 'https://www.heureka.sk/direct/xml-export/shops/heureka-sekce.xml';
		}

		throw new Exception('Heureka is not supported in '.$shop->getLocale()->getRegionName());
	}
}