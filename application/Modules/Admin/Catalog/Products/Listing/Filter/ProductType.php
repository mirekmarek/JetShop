<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Product;


class Listing_Filter_ProductType extends Listing_Filter
{
	protected string $product_type = '';
	
	
	public function getKey(): string
	{
		return static::PRODUCT_TYPE;
	}
	
	public function catchGetParams(): void
	{
		$this->product_type = Http_Request::GET()->getString('product_type', '', array_keys(Product::getProductTypes()));
		if($this->product_type) {
			$this->listing->setGetParam('product_type', $this->product_type);
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
			$this->listing->setGetParam('product_type', $this->product_type);
		} else {
			$this->listing->unsetGetParam('product_type');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->product_type) {
			return;
		}
		
		$this->listing->addWhere([
			'type'   => $this->product_type,
		]);
	}
	
}