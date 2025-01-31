<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\Products;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\KindOfProduct;


class Listing_Filter_ProductKind extends DataListing_Filter
{
	public const KEY = 'product_kind';
	
	protected string $product_kind = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	protected function getOptions() : array
	{
		$_options = KindOfProduct::getScope();
		
		$options =
			[''=>Tr::_(' - all -')] +
			['-1' => Tr::_('- Not set -')] +
			$_options;
		
		return $options;
	}
	
	public function catchParams(): void
	{
		$this->product_kind = Http_Request::GET()->getString('product_kind', '', array_keys($this->getOptions()));
		if($this->product_kind) {
			$this->listing->setParam('product_kind', $this->product_kind);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = $this->getOptions();
		
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
			$this->listing->setParam('product_kind', $this->product_kind);
		} else {
			$this->listing->unsetParam('product_kind');
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
		
		$this->listing->addFilterWhere([
			'kind_id'   => $id,
		]);
	}
	
}