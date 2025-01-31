<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\EventViewer\Shop;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_EventClass extends DataListing_Column
{
	public const KEY = 'event_class';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Event class');
	}
	
	public function getExportHeader(): string
	{
		return $this->getKey();
	}
	
	/**
	 * @param Event $item
	 * @return string
	 */
	public function getExportData( mixed $item ): string
	{
		return $item->getEventClass();
	}
	
}