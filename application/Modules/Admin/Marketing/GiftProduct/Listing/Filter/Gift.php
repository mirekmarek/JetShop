<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Marketing\GiftProduct;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Http_Request;

/**
 *
 */
class Listing_Filter_Gift extends DataListing_Filter {
	
	public const KEY = 'gift';
	
	protected int $gift = 0;
	
	public function getKey(): string
	{
		return static::KEY;
	}

	public function getGift(): int
	{
		return $this->gift;
	}
	
	
	
	public function catchParams(): void
	{
		$this->gift = Http_Request::GET()->getInt( 'gift' );
		$this->listing->setParam( 'gift', $this->gift );
	}
	
	public function catchForm( Form $form ): void
	{
		$this->gift = $form->field( 'gift' )->getValue();
		$this->listing->setParam( 'gift', $this->gift );
	}
	
	public function generateFormFields( Form $form ): void
	{
		$field = new Form_Field_Hidden( 'gift', 'Gift:' );
		$field->setDefaultValue( $this->gift );
		
		$form->addField( $field );
	}
	
	public function generateWhere(): void
	{
		if( $this->gift ) {
			$this->listing->addFilterWhere( [
				'gift_product_id' => $this->gift,
			] );
		}
	}
	
}