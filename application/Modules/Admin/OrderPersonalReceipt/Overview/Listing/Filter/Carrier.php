<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\OrderPersonalReceipt\Overview;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Carrier;


class Listing_Filter_Carrier extends DataListing_Filter
{
	public const KEY = 'carrier';
	
	protected string $carrier = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->carrier = Http_Request::GET()->getString('carrier', '', array_keys( Carrier::getScope() ));
		if($this->carrier) {
			$this->listing->setParam('carrier', $this->carrier);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Carrier::getScope();
		
		$carrier = new Form_Field_Select('carrier', 'Carrier:' );
		$carrier->setDefaultValue( $this->carrier );
		$carrier->setSelectOptions( $options );
		$carrier->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($carrier);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->carrier = $form->field('carrier')->getValue();
		if($this->carrier) {
			$this->listing->setParam('carrier', $this->carrier);
		} else {
			$this->listing->unsetParam('carrier');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->carrier) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'carrier_code'   => $this->carrier,
		]);
	}
	
}