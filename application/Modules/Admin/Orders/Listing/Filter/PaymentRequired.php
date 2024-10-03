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

class Listing_Filter_PaymentRequired extends DataListing_Filter
{
	public const KEY = 'payment_required';
	
	protected string $payment_required = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->payment_required = Http_Request::GET()->getString('payment_required', '', array_keys($this->getScope()));
		if($this->payment_required) {
			$this->listing->setParam('payment_required', $this->payment_required);
		}
	}
	
	public function getScope() : array
	{
		return [
			'yes'  => Tr::_('Yes'),
			'no'   => Tr::_('No'),
		];
		
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + $this->getScope();
		
		$source = new Form_Field_Select('payment_required', 'Payment required:' );
		$source->setDefaultValue( $this->payment_required );
		$source->setSelectOptions( $options );
		$source->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($source);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->payment_required = $form->field('payment_required')->getValue();
		if($this->payment_required) {
			$this->listing->setParam('payment_required', $this->payment_required);
		} else {
			$this->listing->unsetParam('payment_required');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->payment_required) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'payment_required' => $this->payment_required=='yes',
		]);
		
	}
	
}