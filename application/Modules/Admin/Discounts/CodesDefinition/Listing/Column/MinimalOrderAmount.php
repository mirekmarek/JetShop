<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Discounts_Code;

class Listing_Column_MinimalOrderAmount extends Admin_Listing_Column
{
	public const KEY = 'minimal_order_amount';
	
	public function getTitle(): string
	{
		return Tr::_( 'Minimal order amount' );
	}
	
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): float
	{
		/**
		 * @var Discounts_Code $item
		 */
		return $item->getMinimalOrderAmount();
	}
	
}

