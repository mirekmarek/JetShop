<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;


class Listing_Filter_ProductType extends DataListing_Filter
{
	public const KEY = 'product_type';
	
	protected string $product_type = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->product_type = Http_Request::GET()->getString('product_type', '', array_keys(Product::getProductTypes()));
		if($this->product_type) {
			$this->listing->setParam('product_type', $this->product_type);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Product::getProductTypes();
		
		$product_type = new Form_Field_Select('product_type', 'Product type:' );
		$product_type->setDefaultValue( $this->product_type );
		$product_type->setSelectOptions( $options );
		$product_type->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select product type'
		]);
		$form->addField($product_type);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->product_type = $form->field('product_type')->getValue();
		if($this->product_type) {
			$this->listing->setParam('product_type', $this->product_type);
		} else {
			$this->listing->unsetParam('product_type');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->product_type) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'type'   => $this->product_type,
		]);
	}
	
}