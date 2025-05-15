<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Product;
use JetApplication\ProductQuestion;

class Listing_Column_Product extends Admin_Listing_Column
{
	public const KEY = 'product_id';
	
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
	
	public function getExportHeader() : string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ) : string
	{
		/**
		 * @var ProductQuestion $item
		 */
		return Product::get( $item->getProductId() )?->getAdminTitle()??$item->getProductId();
	}
	
}