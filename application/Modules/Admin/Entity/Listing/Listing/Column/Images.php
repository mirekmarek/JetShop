<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\Tr;
use Jet\UI_dataGrid_column;

class Listing_Column_Images extends Listing_Column_Abstract
{
	public const KEY = 'images';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Images', dictionary: Tr::COMMON_DICTIONARY );
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
}