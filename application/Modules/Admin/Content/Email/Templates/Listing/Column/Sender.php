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
use JetApplication\EMail_TemplateText;
use JetApplication\EShops;

class Listing_Column_Sender extends Admin_Listing_Column
{
	public const KEY = 'sender';
	
	public function getTitle(): string
	{
		return Tr::_('Sender');
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
			$titles['email_'.$eshop->getKey()] = Tr::_('Sender - e-mail').' - '.$eshop->getName();
			$titles['name_'.$eshop->getKey()] = Tr::_('Sender - name').' - '.$eshop->getName();
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
			$res['email_'.$eshop->getKey()] = $item->getEshopData( $eshop )->getSenderEmail();
			$res['name_'.$eshop->getKey()] = $item->getEshopData( $eshop )->getSenderName();
		}
		
		return $res;
	}
}