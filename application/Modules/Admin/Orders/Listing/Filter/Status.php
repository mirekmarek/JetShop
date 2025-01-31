<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Order_Status;

class Listing_Filter_Status extends DataListing_Filter
{
	public const KEY = 'status';
	
	protected string $status = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->status = Http_Request::GET()->getString('dispatch_state', '', array_keys($this->getScope()));
		if($this->status) {
			$this->listing->setParam('dispatch_state', $this->status);
		}
	}
	
	public function getScope() : array
	{
		return Order_Status::getScope();
		
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + $this->getScope();
		
		$source = new Form_Field_Select('dispatch_state', 'Dispatch state:' );
		$source->setDefaultValue( $this->status );
		$source->setSelectOptions( $options );
		$source->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($source);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->status = $form->field('dispatch_state')->getValue();
		if($this->status) {
			$this->listing->setParam('dispatch_state', $this->status);
		} else {
			$this->listing->unsetParam('dispatch_state');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->status) {
			return;
		}
		
		
		$this->listing->addFilterWhere( Order_Status::get( $this->status )::getStatusQueryWhere() );
	}
	
}