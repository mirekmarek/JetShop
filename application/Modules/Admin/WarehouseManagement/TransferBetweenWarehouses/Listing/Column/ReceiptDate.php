<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;

class Listing_Column_ReceiptDate extends Admin_Listing_Column
{
	public const KEY = 'receipt_date';
	
	public function getTitle(): string
	{
		return Tr::_('Receipt date');
	}
	
	
}