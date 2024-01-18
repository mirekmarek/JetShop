<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Supplier;


class Listing_Filter_Supplier extends DataListing_Filter
{
	public const KEY = 'supplier';
	
	protected string $supplier = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->supplier = Http_Request::GET()->getString('supplier', '', array_keys(Supplier::getScope()));
		if($this->supplier) {
			$this->listing->setParam('supplier', $this->supplier);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Supplier::getScope();
		
		$supplier = new Form_Field_Select('supplier', 'Supplier:' );
		$supplier->setDefaultValue( $this->supplier );
		$supplier->setSelectOptions( $options );
		$supplier->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($supplier);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->supplier = $form->field('supplier')->getValue();
		if($this->supplier) {
			$this->listing->setParam('supplier', $this->supplier);
		} else {
			$this->listing->unsetParam('supplier');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->supplier) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'supplier_id'   => $this->supplier,
		]);
	}
	
}