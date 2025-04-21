<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;
use JetApplication\WarehouseManagement_Warehouse;


class Listing_Filter_Warehouse extends Admin_Listing_Filter
{
	public const KEY = 'warehouse';
	
	protected string $warehouse = '';
	
	
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
		
		$carrier = new Form_Field_Select('warehouse', 'Warehouse:' );
		$carrier->setDefaultValue( $this->warehouse );
		$carrier->setSelectOptions( $options );
		$carrier->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($carrier);
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