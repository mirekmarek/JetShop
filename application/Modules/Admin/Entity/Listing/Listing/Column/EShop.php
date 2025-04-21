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
use JetApplication\EShopEntity_WithEShopRelation;

class Listing_Column_EShop extends Admin_Listing_Column
{
	public const KEY = 'eshop';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('e-shop', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	
	public function initializer( UI_dataGrid_column $column ) : void
	{
		$column->addCustomCssStyle( 'width:100px;' );
	}
	
	
	public function getExportHeader(): string
	{
		return Tr::_('e-shop', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+shop_code';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-shop_code';
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var EShopEntity_WithEShopRelation $item
		 */
		return $item->getEshop()?->getName()??'';
	}
	
}