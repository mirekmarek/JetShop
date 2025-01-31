<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\AutoOffers;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Http_Request;


class Listing_Filter_Offer extends DataListing_Filter {
	
	public const KEY = 'offer';
	
	protected int $offer = 0;
	
	public function getKey(): string
	{
		return static::KEY;
	}

	public function getOffer(): int
	{
		return $this->offer;
	}
	
	
	
	public function catchParams(): void
	{
		$this->offer = Http_Request::GET()->getInt( 'offer' );
		$this->listing->setParam( 'offer', $this->offer );
	}
	
	public function catchForm( Form $form ): void
	{
		$this->offer = $form->field( 'offer' )->getValue();
		$this->listing->setParam( 'offer', $this->offer );
	}
	
	public function generateFormFields( Form $form ): void
	{
		$field = new Form_Field_Hidden( 'offer', 'Offer:' );
		$field->setDefaultValue( $this->offer );
		
		$form->addField( $field );
	}
	
	public function generateWhere(): void
	{
		if( $this->offer ) {
			$this->listing->addFilterWhere( [
				'offer_product_id' => $this->offer,
			] );
		}
	}
	
}