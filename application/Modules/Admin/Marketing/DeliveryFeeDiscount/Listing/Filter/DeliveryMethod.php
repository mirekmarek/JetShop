<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\DeliveryFeeDiscount;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Delivery_Method;


class Listing_Filter_DeliveryMethod extends Admin_Listing_Filter
{
	
	public const KEY = 'delivery_method';
	
	protected int $delivery_method = 0;

	public function getDeliveryMethod(): int
	{
		return $this->delivery_method;
	}
	
	
	
	public function catchParams(): void
	{
		$this->delivery_method = Http_Request::GET()->getInt( 'delivery_method' );
		$this->listing->setParam( 'delivery_method', $this->delivery_method );
	}
	
	public function catchForm( Form $form ): void
	{
		$this->delivery_method = $form->field( 'delivery_method' )->getValue();
		$this->listing->setParam( 'delivery_method', $this->delivery_method );
	}
	
	public function generateFormFields( Form $form ): void
	{
		$field = new Form_Field_Select( 'delivery_method', 'Delivery method:' );
		$field->setSelectOptions( [0=>Tr::_('- all -')]+Delivery_Method::getScope() );
		$field->setDefaultValue( $this->delivery_method );
		
		$form->addField( $field );
	}
	
	public function generateWhere(): void
	{
		if( $this->delivery_method ) {
			$this->listing->addFilterWhere( [
				'delivery_method_id' => $this->delivery_method,
			] );
		}
	}
	
}