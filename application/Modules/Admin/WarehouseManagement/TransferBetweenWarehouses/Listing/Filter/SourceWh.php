<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\WarehouseManagement_Warehouse;

class Listing_Filter_SourceWh extends DataListing_Filter
{
	public const KEY = 'source_wh';
	
	protected string $source_wh = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->source_wh = Http_Request::GET()->getString('source_wh', '', array_keys(WarehouseManagement_Warehouse::getScope()));
		if($this->source_wh) {
			$this->listing->setParam('source_wh', $this->source_wh);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + WarehouseManagement_Warehouse::getScope();
		
		$status = new Form_Field_Select('source_wh', 'Source warehouse:' );
		$status->setDefaultValue( $this->source_wh );
		$status->setSelectOptions( $options );
		$status->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($status);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->source_wh = $form->field('source_wh')->getValue();
		if($this->source_wh) {
			$this->listing->setParam('source_wh', $this->source_wh);
		} else {
			$this->listing->unsetParam('source_wh');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->source_wh) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'source_warehouse_id'   => $this->source_wh,
		]);
	}
	
}