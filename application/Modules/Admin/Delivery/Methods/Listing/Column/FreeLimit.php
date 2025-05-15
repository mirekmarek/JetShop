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

class Listing_Column_FreeLimit extends Admin_Listing_Column
{
	public const KEY = 'free_delivery_limit';
	
	public function getTitle(): string
	{
		return Tr::_('Delivery free limit');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:250px');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): float
	{
		/**
		 * @var Delivery_Method $item
		 */
		return $item->getFreeDeliveryLimit();
	}
}