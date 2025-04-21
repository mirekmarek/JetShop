<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;

class Listing_Filter_Paid extends Admin_Listing_Filter
{
	public const KEY = 'paid';
	
	protected string $paid = '';
	
	public function catchParams(): void
	{
		$this->paid = Http_Request::GET()->getString('paid', '', array_keys($this->getScope()));
		if($this->paid) {
			$this->listing->setParam('paid', $this->paid);
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
		
		$source = new Form_Field_Select('paid', 'Paid:' );
		$source->setDefaultValue( $this->paid );
		$source->setSelectOptions( $options );
		$source->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($source);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->paid = $form->field('paid')->getValue();
		if($this->paid) {
			$this->listing->setParam('paid', $this->paid);
		} else {
			$this->listing->unsetParam('paid');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->paid) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'paid' => $this->paid=='yes',
		]);
		
	}
	
}