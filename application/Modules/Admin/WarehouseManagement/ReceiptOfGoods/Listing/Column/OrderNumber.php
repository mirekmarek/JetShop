<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_OrderNumber extends DataListing_Column
{
	public const KEY = 'order_number';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Order number');
	}
	
}