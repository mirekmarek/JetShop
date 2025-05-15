<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\EShopEntity_HasStatus_Interface;

class Listing_Column_Status extends Admin_Listing_Column
{
	public const KEY = 'status';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Status');
	}
	
	public function getExportHeader() : string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ) : string
	{
		/**
		 * @var EShopEntity_HasStatus_Interface $item
		 */
		return $item->getStatus()?->getTitle()??'';
	}
	
}