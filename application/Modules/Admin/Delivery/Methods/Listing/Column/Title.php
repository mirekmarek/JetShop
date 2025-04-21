<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Delivery\Methods;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Delivery_Method;

class Listing_Column_Title extends Admin_Listing_Column
{
	public const KEY = 'title';
	
	public function getTitle(): string
	{
		return Tr::_('Title');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:350px');
	}
	
	public function getExportHeader(): null|string|array
	{
		return Tr::_('Title');
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Delivery_Method $item
		 */
		return $item->getTitle();
	}
}