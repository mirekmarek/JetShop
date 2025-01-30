<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\EShopEntity_WithEShopData;

class Listing_Column_InternalCode extends Listing_Column_Abstract
{
	public const KEY = 'internal_code';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Internal code', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	
	public function initializer( UI_dataGrid_column $column ) : void
	{
		$column->addCustomCssStyle( 'width:120px;' );
	}
	
	
	public function getExportHeader(): string
	{
		return Tr::_('Internal code', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var EShopEntity_WithEShopData $item
		 */
		return $item->getInternalCode();
	}
	
	
}