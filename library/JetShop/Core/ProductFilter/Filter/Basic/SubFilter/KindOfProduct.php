<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form_Field;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\KindOfProduct;
use JetApplication\Product_EShopData;
use JetApplication\ProductFilter_Filter_Basic_SubFilter;

abstract class Core_ProductFilter_Filter_Basic_SubFilter_KindOfProduct extends ProductFilter_Filter_Basic_SubFilter
{
	protected static string $key = 'kind_of_product_id';
	protected string $label = 'Kind of product:';
	
	public function getEditField() : ?Form_Field
	{
		$field = new Form_Field_Select( $this::getKey(), $this->getLabel() );
		
		if($this->filter_value===null) {
			$field->setDefaultValue( '' );
		} else {
			$field->setDefaultValue( $this->filter_value );
		}
		$field->setSelectOptions(
			['' => Tr::_('- all -')]+
			KindOfProduct::getScope()
		);
		$field->setFieldValueCatcher( function( $value ) {
			$value = (int)$value;
			$value = $value?:null;
			$this->setFiltervalue( $value );
		} );
		
		return $field;
	}
	
	public function filter(): void
	{
		$where = $this->filter->getProductFilter()->getEshop()->getWhere();
		
		if($this->previous_filter_result!==null) {
			$where[] = 'AND';
			$where['entity_id'] = $this->previous_filter_result;
		}
		
		$where[] = 'AND';
		$where['kind_id'] = $this->filter_value;
		
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