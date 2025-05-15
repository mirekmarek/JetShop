<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\ReturnOfGoods;

class Listing_Column_DateStarted extends Admin_Listing_Column
{
	public const KEY = 'date_started';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Date and time');
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): object
	{
		/**
		 * @var ReturnOfGoods $item
		 */
		return $item->getDateStarted();
	}
	
}