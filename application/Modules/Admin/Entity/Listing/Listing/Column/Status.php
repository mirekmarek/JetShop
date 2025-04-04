<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\Tr;
use JetApplication\EShopEntity_HasStatus_Interface;

class Listing_Column_Status extends Listing_Column_Abstract
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
	
	public function getExportHeader() : null|string|array
	{
		return Tr::_('Number');
	}
	
	public function getExportData( mixed $item ) : float|int|bool|string|array
	{
		/**
		 * @var EShopEntity_HasStatus_Interface $item
		 */
		return $item->getStatus()->getTitle();
	}
	
}