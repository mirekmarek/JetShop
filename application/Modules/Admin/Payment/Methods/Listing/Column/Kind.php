<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Payment_Method;

class Listing_Column_Kind extends Admin_Listing_Column
{
	public const KEY = 'kind';
	
	public function getTitle(): string
	{
		return Tr::_('Kind of payment method');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:350px');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ):string
	{
		/**
		 * @var Payment_Method $item
		 */
		return $item->getKindTitle();
	}
}