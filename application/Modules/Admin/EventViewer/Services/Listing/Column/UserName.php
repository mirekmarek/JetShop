<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\EventViewer\Services;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_UserName extends DataListing_Column
{
	public const KEY = 'user_name';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('User name');
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
		return $item->getUserUsername();
	}
	
}