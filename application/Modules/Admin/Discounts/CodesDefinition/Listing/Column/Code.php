<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Discounts_Code;

class Listing_Column_Code extends Admin_Listing_Column
{
	public const KEY = 'code';
	
	public function getTitle(): string
	{
		return Tr::_( 'Code' );
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->setBaseCssClass("width: 250px;");
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Discounts_Code $item
		 */
		return $item->getCode();
	}
}