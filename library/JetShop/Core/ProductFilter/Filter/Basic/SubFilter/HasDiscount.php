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
use JetApplication\Product_Price;
use JetApplication\ProductFilter_Filter_Basic_SubFilter;

abstract class Core_ProductFilter_Filter_Basic_SubFilter_HasDiscount extends ProductFilter_Filter_Basic_SubFilter
{
	protected static string $key = 'has_discount';
	protected string $label = 'Has discount:';
	
	public function getEditField() : ?Form_Field
	{
		$field = new Form_Field_Select( $this::getKey(), $this->getLabel() );
		
		if($this->filter_value===null) {
			$field->setDefaultValue( '' );
		} else {
			$field->setDefaultValue( $this->filter_value ? '1' : '0' );
		}
		$field->setSelectOptions([
			'' => Tr::_('- all -'),
			'1' => Tr::_('Has discount'),
			'0' => Tr::_('Without discount'),
		]);
		$field->setFieldValueCatcher( function( $value ) {
			$this->setFiltervalue( match ($value) {
				'' => null,
				'0' => false,
				'1' => true
			} );
		} );
		
		return $field;
	}
	
	public function filter(): void
	{
		if($this->previous_filter_result===null) {
			//It is not allowed to load full pricelist due to security (pricelist may be really huge)
			$filter_result = [];
		} else {
			$filter_result = Product_Price::filterHasDiscount( $this->getPricelist(), $this->previous_filter_result, $this->filter_value );
		}
		
		$this->filter_result = $filter_result?:[];
		
	}
	
	public function itMakeSenseForCustomer() : ?bool
	{
		$i_ids = $this->filter->getInitialProductIds();
		if(!$i_ids) {
			return false;
		}
		
		$filter_result = Product_Price::filterHasDiscount( $this->getPricelist(), $i_ids, true );
		if(
			count($filter_result)==0 ||
			count($filter_result)==count($i_ids)
		) {
			return false;
		}
		
		return true;
	}
	
	
	public function getURLParam() : string
	{
		return 'has-discount';
	}
	
	public function isForCustomerUI() : bool
	{
		return true;
	}
	
}