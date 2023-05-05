<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Exports\GoogleShopping;

use Jet\BaseObject;
use JetApplication\Shops_Shop;

class Config extends BaseObject {

	public static function getCategoriesURL( Shops_Shop $shop ) : string
	{
		$locale = $shop->getLocale();

		$lng = $locale->getLanguage();
		$reg = $locale->getRegion();

		return 'https://www.google.com/basepages/producttype/taxonomy-with-ids.'.$lng.'-'.$reg.'.txt';

	}
}