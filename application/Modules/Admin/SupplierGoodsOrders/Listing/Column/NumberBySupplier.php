<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\SupplierGoodsOrders;


use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;

class Listing_Column_NumberBySupplier extends Admin_Listing_Column
{
	public const KEY = 'number_by_supplier';
	
	public function getTitle(): string
	{
		return Tr::_('Supplier\'s order number');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		//$column->addCustomCssStyle('width: 200px');
	}
	
}