<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Orders;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;

class Listing_Filter_AllItemsAvailable extends DataListing_Filter
{
	public const KEY = 'all_items_available';
	
	protected string $all_items_available = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->all_items_available = Http_Request::GET()->getString('all_items_available', '', array_keys($this->getScope()));
		if($this->all_items_available) {
			$this->listing->setParam('all_items_available', $this->all_items_available);
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
		
		$source = new Form_Field_Select('all_items_available', 'All items available:' );
		$source->setDefaultValue( $this->all_items_available );
		$source->setSelectOptions( $options );
		$source->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($source);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->all_items_available = $form->field('all_items_available')->getValue();
		if($this->all_items_available) {
			$this->listing->setParam('all_items_available', $this->all_items_available);
		} else {
			$this->listing->unsetParam('all_items_available');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->all_items_available) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'all_items_available' => $this->all_items_available=='yes',
		]);
		
	}
	
}