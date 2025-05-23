<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Property;

class Listing_Column_Type extends Admin_Listing_Column
{
	public const KEY = 'type';
	
	public function getTitle(): string
	{
		return Tr::_('Type');
	}
	
	public function initializer( UI_dataGrid_column $column ) : void
	{
		$column->addCustomCssStyle( 'width:130px;' );
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Property $item
		 */
		return $item->getType();
	}
	
}