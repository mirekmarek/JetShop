<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Brand;


class Listing_Filter_Brand extends DataListing_Filter
{
	public const KEY = 'brand';
	
	protected string $brand = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->brand = Http_Request::GET()->getString('brand', '', array_keys(Brand::getScope()));
		if($this->brand) {
			$this->listing->setParam('brand', $this->brand);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Brand::getScope();
		
		$brand = new Form_Field_Select('brand', 'Brand:' );
		$brand->setDefaultValue( $this->brand );
		$brand->setSelectOptions( $options );
		$brand->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($brand);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->brand = $form->field('brand')->getValue();
		if($this->brand) {
			$this->listing->setParam('brand', $this->brand);
		} else {
			$this->listing->unsetParam('brand');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->brand) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'brand_id'   => $this->brand,
		]);
	}
	
}