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
use JetApplication\ProductQuestion;

class Listing_Column_AuthorEmail extends DataListing_Column
{
	public const KEY = 'author_email';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Author - e-mail');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
	}
	
	public function getExportHeader() : null|string|array
	{
		return Tr::_('Author - e-mail');
	}
	
	public function getExportData( mixed $item ) : float|int|bool|string|array
	{
		/**
		 * @var ProductQuestion $item
		 */
		return $item->getAuthorEmail();
	}
	
}