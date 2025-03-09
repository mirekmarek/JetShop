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

class Listing_Column_Answered extends DataListing_Column
{
	public const KEY = 'answered';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Answered');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:80px');
	}
	
	public function getExportHeader() : null|string|array
	{
		return Tr::_('Answered');
	}
	
	public function getExportData( mixed $item ) : float|int|bool|string|array
	{
		/**
		 * @var ProductQuestion $item
		 */
		return Tr::_($item->isAnswered() ?'Yes':'No', dictionary: Tr::COMMON_DICTIONARY);
	}
	
}