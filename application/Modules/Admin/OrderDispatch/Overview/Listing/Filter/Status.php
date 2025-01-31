<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderDispatch\Overview;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\OrderDispatch;


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
		$this->status = Http_Request::GET()->getString('status', '', array_keys(OrderDispatch::getStatusScope()));
		if($this->status) {
			$this->listing->setParam('status', $this->status);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + OrderDispatch::getStatusScope();
		
		$status = new Form_Field_Select('status', 'Status:' );
		$status->setDefaultValue( $this->status );
		$status->setSelectOptions( $options );
		$status->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($status);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->status = $form->field('status')->getValue();
		if($this->status) {
			$this->listing->setParam('status', $this->status);
		} else {
			$this->listing->unsetParam('status');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->status) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'status'   => $this->status,
		]);
	}
	
}