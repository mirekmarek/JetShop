<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\KindOfProduct;
use JetApplication\Supplier;
use JetApplication\WarehouseManagement_StockVerification;

class Listing_Column_Criteria extends Admin_Listing_Column
{
	public const KEY = 'criteria';
	
	public function getTitle(): string
	{
		return Tr::_('Criteria');
	}
	
	public function getDisallowSort() : bool
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
		 * @var WarehouseManagement_StockVerification $item
		 */
		$res = '';
		
		if($item->getCriteriaSupplierId()) {
			$res .= Tr::_('Supplier:').Supplier::getScope()[$item->getCriteriaSupplierId()]."\n";
		}
		
		
		if($item->getCriteriaKindOfProductId()) {
			$res .= Tr::_('Kind of product:').KindOfProduct::getScope()[$item->getCriteriaKindOfProductId()]."\n";
		}
		
		if($item->getCriteriaSector()) {
			$res .= Tr::_('Sector:').$item->getCriteriaSector()."\n";
		}
		
		if($item->getCriteriaRack()) {
			$res .= Tr::_('Rack:').$item->getCriteriaRack()."\n";
		}
		
		if($item->getCriteriaPosition()) {
			$res .= Tr::_('Position:').$item->getCriteriaPosition()."\n";
		}
	
		return $res;
	}
}