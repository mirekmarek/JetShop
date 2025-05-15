<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Email\Templates;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\EMail_Layout;
use JetApplication\EMail_TemplateText;
use JetApplication\EShops;

class Listing_Column_Layout extends Admin_Listing_Column
{
	public const KEY = 'layout';
	
	public function getTitle(): string
	{
		return Tr::_('Layout');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:300px');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function getExportHeader(): array
	{
		$titles = [];
		foreach(EShops::getListSorted() as $eshop) {
			$titles['layout_'.$eshop->getKey()] = Tr::_('Layout script').' - '.$eshop->getName();
		}
		
		return $titles;

	}
	
	public function getExportData( mixed $item ): array
	{
		/**
		 * @var EMail_TemplateText $item
		 */
		$res = [];
		
		foreach(EShops::getListSorted() as $eshop) {
			$res['layout_'.$eshop->getKey()] = EMail_Layout::getScope()[$item->getEshopData($eshop)->getLayoutId()]??'';
		}
		
		return $res;
	}
}