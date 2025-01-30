<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\Tr;
use Jet\UI_dataGrid_column;

class Listing_Column_ValidTill extends Listing_Column_Abstract
{
	public const KEY = 'valid_till';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Valid till', dictionary: Tr::COMMON_DICTIONARY );
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width: 200px');
	}
	
}