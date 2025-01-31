<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\BaseObject;

abstract class Core_EShopEntity_Definition_Property extends BaseObject
{
	protected ?bool $is_description = null;
	protected ?string $setter = null;
	
	public static function read( array $attributes ) : static
	{
		$def = new static();
		
		foreach(get_object_vars($def) as $key => $value) {
			$def->$key = $attributes[$key]??null;
		}
		
		return $def;
	}
	
	public function isDescription(): ?bool
	{
		return $this->is_description;
	}
	
	public function getSetter(): ?string
	{
		return $this->setter;
	}
	
	
}