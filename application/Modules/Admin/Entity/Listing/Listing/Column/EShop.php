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
use JetApplication\Entity_WithEShopRelation;

class Listing_Column_EShop extends Listing_Column_Abstract
{
	public const KEY = 'eshop';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('e-shop');
	}
	
	
	public function initializer( UI_dataGrid_column $column ) : void
	{
		$column->addCustomCssStyle( 'width:100px;' );
	}
	
	
	public function getExportHeader(): string
	{
		return Tr::_('e-shop');
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+shop_code';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-shop_code';
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Entity_WithEShopRelation $item
		 */
		return $item->getEshopKey();
	}
	
}