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
use JetApplication\EShopEntity_WithEShopData;

class Listing_Column_InternalName extends Admin_Listing_Column
{
	public const KEY = 'internal_name';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Internal name', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	
	public function initializer( UI_dataGrid_column $column ) : void
	{
		$column->addCustomCssStyle( 'width:250px;' );
	}
	
	
	public function getExportHeader(): string
	{
		return Tr::_('Internal name', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var EShopEntity_WithEShopData $item
		 */
		return $item->getInternalName();
	}
	
	
}