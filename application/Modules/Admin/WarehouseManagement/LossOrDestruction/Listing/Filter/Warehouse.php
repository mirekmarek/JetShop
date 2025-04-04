<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\WarehouseManagement_Warehouse;


class Listing_Filter_Warehouse extends DataListing_Filter
{
	public const KEY = 'warehouse';
	
	protected string $warehouse = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->warehouse = Http_Request::GET()->getString('warehouse', '', array_keys( WarehouseManagement_Warehouse::getScope() ));
		if($this->warehouse) {
			$this->listing->setParam('warehouse', $this->warehouse);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + WarehouseManagement_Warehouse::getScope();
		
		$warehouse = new Form_Field_Select('warehouse', 'Warehouse:' );
		$warehouse->setDefaultValue( $this->warehouse );
		$warehouse->setSelectOptions( $options );
		$warehouse->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($warehouse);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->warehouse = $form->field('warehouse')->getValue();
		if($this->warehouse) {
			$this->listing->setParam('warehouse', $this->warehouse);
		} else {
			$this->listing->unsetParam('warehouse');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->warehouse) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'warehouse_id'   => $this->warehouse,
		]);
	}
	
}