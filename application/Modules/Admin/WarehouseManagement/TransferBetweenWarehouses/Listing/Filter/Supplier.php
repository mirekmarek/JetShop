<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Supplier;

class Listing_Filter_Supplier extends Admin_Listing_Filter
{
	public const KEY = 'supplier';
	
	protected int $supplier = 0;
	
	public function catchParams(): void
	{
		$this->supplier = (int)Http_Request::GET()->getString('supplier', 0, array_keys(Supplier::getScope()));
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
		$this->supplier = (int)$form->field('supplier')->getValue();
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