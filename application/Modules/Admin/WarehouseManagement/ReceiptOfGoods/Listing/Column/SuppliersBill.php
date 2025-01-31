<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\WarehouseManagement\ReceiptOfGoods;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_SuppliersBill extends DataListing_Column
{
	public const KEY = 'suppliers_bill_number';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Supplier\'s bill');
	}
	
}