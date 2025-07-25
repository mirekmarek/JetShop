<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\EShopEntity_HasActivationByTimePlan_Interface;

class Listing_Column_ValidFrom extends Admin_Listing_Column
{
	public const KEY = 'valid_from';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Valid from', dictionary: Tr::COMMON_DICTIONARY );
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width: 200px');
	}
	
	public function getExportHeader() : string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ) : object|string
	{
		/**
		 * @var EShopEntity_HasActivationByTimePlan_Interface $item
		 */
		return $item->getActiveFrom()??'';
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+active_from';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-active_from';
	}
	
	
}