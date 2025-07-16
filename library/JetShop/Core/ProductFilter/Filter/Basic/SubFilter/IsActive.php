<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form_Field;
use JetApplication\Product_EShopData;
use JetApplication\ProductFilter_Filter_Basic_SubFilter;

abstract class Core_ProductFilter_Filter_Basic_SubFilter_IsActive extends ProductFilter_Filter_Basic_SubFilter
{
	protected static string $key = 'item_is_active';
	
	
	public function getEditField() : ?Form_Field
	{
		return null;
	}
	
	public function filter(): void
	{
		$where = $this->filter->getProductFilter()->getEshop()->getWhere();
		
		if($this->previous_filter_result!==null) {
			$where[] = 'AND';
			$where['entity_id'] = $this->previous_filter_result;
		}
		
		$where[] = 'AND';
		if($this->filter_value) {
			$where[] = [
				'entity_is_active' => true,
				'AND',
				'is_active_for_eshop' => true
			];
		} else {
			$where[] = [
				'entity_is_active' => false,
				'OR',
				'is_active_for_eshop' => false
			];
		}
		
		$filter_result = Product_EShopData::dataFetchCol(
			select: ['entity_id'],
			where: $where,
			raw_mode: true
		);
		
		$this->filter_result = $filter_result?:[];
	}
	
	public function itMakeSenseForCustomer() : ?bool
	{
		return false;
	}
	
	public function getURLParam() : string
	{
		return '';
	}
	
	public function isForCustomerUI() : bool
	{
		return false;
	}
	
}