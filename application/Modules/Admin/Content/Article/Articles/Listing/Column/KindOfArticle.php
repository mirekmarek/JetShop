<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Content_Article;
use JetApplication\Content_Article_KindOfArticle;

class Listing_Column_KindOfArticle extends Admin_Listing_Column
{
	public const KEY = 'kind_of_article';
	
	public function getTitle(): string
	{
		return Tr::_('Kind of Article');
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
		 * @var Content_Article $item
		 */
		
		return Content_Article_KindOfArticle::getScope()[$item->getKindId()]??'';
	}
}