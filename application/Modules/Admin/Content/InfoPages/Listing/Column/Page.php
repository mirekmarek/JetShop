<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\InfoPages;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Content_InfoPage;

class Listing_Column_Page extends Admin_Listing_Column
{
	public const KEY = 'page_id';
	
	public function getTitle(): string
	{
		return Tr::_('Page');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:250px');
	}
	
	public function getExportHeader(): string
	{
		return Tr::_('Page ID');
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Content_InfoPage $iten
		 */
		return $iten->getPageId();
	}
}