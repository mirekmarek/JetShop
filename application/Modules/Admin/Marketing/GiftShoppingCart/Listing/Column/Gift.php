<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\GiftShoppingCart;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Marketing_Gift_ShoppingCart;
use JetApplication\Product;

class Listing_Column_Gift extends Admin_Listing_Column
{
	public const KEY = 'gift_product_id';
	
	public function getTitle(): string
	{
		return Tr::_('Gift');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:200px;');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Marketing_Gift_ShoppingCart $item
		 */
		
		return Product::get( $item->getGiftProductId() )?->getAdminTitle()??'';
	}
}