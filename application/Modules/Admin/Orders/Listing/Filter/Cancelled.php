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

class Listing_Filter_Cancelled extends DataListing_Filter
{
	public const KEY = 'cancelled';
	
	protected string $cancelled = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->cancelled = Http_Request::GET()->getString('cancelled', '', array_keys($this->getScope()));
		if($this->cancelled) {
			$this->listing->setParam('cancelled', $this->cancelled);
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
		
		$source = new Form_Field_Select('cancelled', 'Cancelled:' );
		$source->setDefaultValue( $this->cancelled );
		$source->setSelectOptions( $options );
		$source->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($source);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->cancelled = $form->field('cancelled')->getValue();
		if($this->cancelled) {
			$this->listing->setParam('cancelled', $this->cancelled);
		} else {
			$this->listing->unsetParam('cancelled');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->cancelled) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'cancelled' => $this->cancelled=='yes',
		]);
		
	}
	
}