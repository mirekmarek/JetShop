<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;


use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Product;
use JetApplication\ProductQuestion;

class Listing_Column_Product extends DataListing_Column
{
	public const KEY = 'product_id';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Product');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function getExportHeader() : null|string|array
	{
		return Tr::_('Created');
	}
	
	public function getExportData( mixed $item ) : float|int|bool|string|array
	{
		/**
		 * @var ProductQuestion $item
		 */
		return Product::get( $item->getProductId() )?->getAdminTitle()??$item->getProductId();
	}
	
}