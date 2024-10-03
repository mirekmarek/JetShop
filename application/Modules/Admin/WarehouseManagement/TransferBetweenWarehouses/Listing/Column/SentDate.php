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

class Listing_Column_SentDate extends DataListing_Column
{
	public const KEY = 'sent_date';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Sent');
	}
	

}