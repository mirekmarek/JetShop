<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Delivery\Methods;


use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Delivery_Method;

class Listing_Column_Carrier extends DataListing_Column
{
	public const KEY = 'carrier';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Carrier module');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:350px');
	}
	
	public function getExportHeader(): null|string|array
	{
		return Tr::_('Carrier module');
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Delivery_Method $item
		 */
		return $item->getCarrier()?->getName()??'';
	}
}