<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\EventViewer\Services;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_ContextObjectId extends DataListing_Column
{
	public const KEY = 'context_object_id';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Context object ID');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	/**
	 * @var Event $item
	 * @return string
	 */
	public function getExportData( mixed $item ): string
	{
		return $item->getContextObjectId();
	}
}