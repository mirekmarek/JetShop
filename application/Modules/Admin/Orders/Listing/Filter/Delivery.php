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
use JetApplication\Delivery_Method;

class Listing_Filter_Delivery extends DataListing_Filter
{
	public const KEY = 'delivery';
	
	protected string $delivery = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->delivery = Http_Request::GET()->getString('delivery', '', array_keys(Delivery_Method::getScope()));
		if($this->delivery) {
			$this->listing->setParam('delivery', $this->delivery);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Delivery_Method::getScope();
		
		$delivery = new Form_Field_Select('delivery', 'Delivery method:' );
		$delivery->setDefaultValue( $this->delivery );
		$delivery->setSelectOptions( $options );
		$delivery->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($delivery);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->delivery = $form->field('delivery')->getValue();
		if($this->delivery) {
			$this->listing->setParam('delivery', $this->delivery);
		} else {
			$this->listing->unsetParam('delivery');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->delivery) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'delivery_method_id'   => $this->delivery,
		]);
	}
	
}