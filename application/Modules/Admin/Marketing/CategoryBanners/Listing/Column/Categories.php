<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\CategoryBanners;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Marketing_CategoryBanner;

class Listing_Column_Categories extends Admin_Listing_Column
{
	public const KEY = 'categories';
	
	public function getTitle(): string
	{
		return Tr::_('Categories');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Marketing_CategoryBanner $item
		 */
		
		$categories = $item->getCategories();
		$res = [];
		
		foreach($categories as $category) {
			$res[] = $category->getAdminTitle();
		}
		
		return implode(', ', $res);
	}
}