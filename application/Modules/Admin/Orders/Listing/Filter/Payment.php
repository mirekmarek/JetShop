<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Payment_Method;

class Listing_Filter_Payment extends DataListing_Filter
{
	public const KEY = 'payment';
	
	protected string $payment = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->payment = Http_Request::GET()->getString('payment', '', array_keys(Payment_Method::getScope()));
		if($this->payment) {
			$this->listing->setParam('payment', $this->payment);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Payment_Method::getScope();
		
		$payment = new Form_Field_Select('payment', 'Payment method:' );
		$payment->setDefaultValue( $this->payment );
		$payment->setSelectOptions( $options );
		$payment->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($payment);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->payment = $form->field('payment')->getValue();
		if($this->payment) {
			$this->listing->setParam('payment', $this->payment);
		} else {
			$this->listing->unsetParam('payment');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->payment) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'payment_method_id'   => $this->payment,
		]);
	}
	
}