<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;


use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

class Listing_Column_SentDate extends Admin_Listing_Column
{
	public const KEY = 'sent_date';
	
	public function getTitle(): string
	{
		return Tr::_('Sent');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object|string
	{
		/**
		 * @var WarehouseManagement_TransferBetweenWarehouses $item
		 */
		return $item->getSentDateTime()??'';
	}
	

}