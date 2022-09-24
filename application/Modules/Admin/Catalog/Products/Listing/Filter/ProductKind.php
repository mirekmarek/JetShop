<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetShop\KindOfProduct;
use JetShop\Product;


class Listing_Filter_ProductKind extends Data_Listing_Filter
{
	protected string $product_kind = '';
	
	public function catchGetParams(): void
	{
		$this->product_kind = Http_Request::GET()->getString('product_kind', '', array_keys(
			['-1' => Tr::_('- Not set -')] +
			KindOfProduct::getScope()
		));
		if($this->product_kind) {
			$this->listing->setGetParam('product_kind', $this->product_kind);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options =
			[''=>Tr::_(' - all -')] +
			['-1' => Tr::_('- Not set -')] +
			KindOfProduct::getScope();
		
		$product_kind = new Form_Field_Select('product_kind', 'Kind of product:' );
		$product_kind->setDefaultValue( $this->product_kind );
		$product_kind->setSelectOptions( $options );
		$product_kind->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind of product'
		]);
		$form->addField($product_kind);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->product_kind = $form->field('product_kind')->getValue();

		if($this->product_kind) {
			$this->listing->setGetParam('product_kind', $this->product_kind);
		} else {
			$this->listing->unsetGetParam('product_kind');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->product_kind) {
			return;
		}
		
		$id = $this->product_kind;
		
		if($id=='-1') {
			$id = 0;
		}
		
		$this->listing->addWhere([
			'kind_id'   => $id,
		]);
	}
	
}