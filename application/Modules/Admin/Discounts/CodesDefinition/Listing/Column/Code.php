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
}