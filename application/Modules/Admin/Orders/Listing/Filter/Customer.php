<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Http_Request;
use JetApplication\Admin_Listing_Filter;

class Listing_Filter_Customer extends Admin_Listing_Filter
{
	public const KEY = 'customer';
	
	protected int $customer_id = 0;
	

	public function getCustomerId(): int
	{
		return $this->customer_id;
	}
	
	
	
	public function catchParams(): void
	{
		$this->customer_id = Http_Request::GET()->getInt('customer');
		if($this->customer_id) {
			$this->listing->setParam('customer', $this->customer_id);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$status = new Form_Field_Input('customer', 'Customer' );
		$status->setDefaultValue( $this->customer_id?:'' );
		$form->addField($status);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->customer_id = (int)$form->field('customer')->getValue();
		if($this->customer_id) {
			$this->listing->setParam('customer', $this->customer_id);
		} else {
			$this->listing->unsetParam('customer');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->customer_id) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'customer_id'   => $this->customer_id,
		]);
	}
	
	public function isActive(): bool
	{
		return $this->customer_id > 0;
	}
}