<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\PromoAreas;


use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;

class Listing_Column_Area extends DataListing_Column
{
	public const KEY = 'area';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Area');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:200px;');
	}
}