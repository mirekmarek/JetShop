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
use JetApplication\WarehouseManagement_Warehouse;

class Listing_Filter_TargetWh extends Admin_Listing_Filter
{
	public const KEY = 'target_wh';
	
	protected string $target_wh = '';
	
	public function catchParams(): void
	{
		$this->target_wh = Http_Request::GET()->getString('target_wh', '', array_keys(WarehouseManagement_Warehouse::getScope()));
		if($this->target_wh) {
			$this->listing->setParam('target_wh', $this->target_wh);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + WarehouseManagement_Warehouse::getScope();
		
		$status = new Form_Field_Select('target_wh', 'Target warehouse:' );
		$status->setDefaultValue( $this->target_wh );
		$status->setSelectOptions( $options );
		$status->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($status);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->target_wh = $form->field('target_wh')->getValue();
		if($this->target_wh) {
			$this->listing->setParam('target_wh', $this->target_wh);
		} else {
			$this->listing->unsetParam('target_wh');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->target_wh) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'target_warehouse_id'   => $this->target_wh,
		]);
	}
	
}