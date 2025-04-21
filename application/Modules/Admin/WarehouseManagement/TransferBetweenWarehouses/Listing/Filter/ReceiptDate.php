<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_Date;
use Jet\Http_Request;
use JetApplication\Admin_Listing_Filter;

class Listing_Filter_ReceiptDate extends Admin_Listing_Filter
{
	public const KEY = 'receipt_date';
	
	protected ?Data_DateTime $from = null;
	protected ?Data_DateTime $till = null;
	

	public function getFrom(): ?Data_DateTime
	{
		return $this->from;
	}
	

	public function getTill(): ?Data_DateTime
	{
		return $this->till;
	}
	
	
	
	public function catchParams(): void
	{
		$this->from = Data_DateTime::catchDate( Http_Request::GET()->getString('date_from') );
		if($this->from) {
			$this->listing->setParam('date_from', (string)$this->from);
		}
		
		$this->till = Data_DateTime::catchDate( Http_Request::GET()->getString('date_till') );
		if($this->till) {
			$this->listing->setParam('date_till', (string)$this->till);
		}
		
	}
	
	public function generateFormFields( Form $form ): void
	{
		$from = new Form_Field_Date('date_from', 'From:' );
		$from->setDefaultValue( $this->from );
		$from->setErrorMessages([
			Form_Field_Date::ERROR_CODE_INVALID_VALUE => 'Invalid value',
			Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid value',
		]);
		$form->addField($from);
		
		$till = new Form_Field_Date('date_till', 'Till:' );
		$till->setDefaultValue( $this->till );
		$till->setErrorMessages([
			Form_Field_Date::ERROR_CODE_INVALID_VALUE => 'Invalid value',
			Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid value',
		]);
		$form->addField($till);
		
	}
	
	public function catchForm( Form $form ): void
	{
		
		$this->from = Data_DateTime::catchDate( $form->field('date_from')->getValue() );
		if($this->from) {
			$this->listing->setParam('date_from', (string)$this->from);
		} else {
			$this->listing->unsetParam('date_from');
		}
		
		$this->till = Data_DateTime::catchDate( $form->field('date_till')->getValue() );
		if($this->till) {
			$this->listing->setParam('date_till', (string)$this->till);
		} else {
			$this->listing->unsetParam('date_till');
		}
		
	}
	
	public function generateWhere(): void
	{
		if($this->from) {
			$this->listing->addFilterWhere([
				'receipt_date_time >='   => $this->from.' 00:00:00',
			]);
		}
		
		if($this->till) {
			$this->listing->addFilterWhere([
				'receipt_date_time <='   => $this->till.' 23:59:59',
			]);
		}
		
		
	}
	
}