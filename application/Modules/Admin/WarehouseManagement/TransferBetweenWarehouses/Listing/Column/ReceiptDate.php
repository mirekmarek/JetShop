<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_ReceiptDate extends DataListing_Column
{
	public const KEY = 'receipt_date';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Receipt date');
	}
	
	
}