<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Http_Request;

class Listing_Filter_Product extends DataListing_Filter
{
	public const KEY = 'product';
	
	protected int $product_id = 0;
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->product_id = Http_Request::GET()->getInt('product');
		if($this->product_id) {
			$this->listing->setParam('product', $this->product_id);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$product = new Form_Field_Hidden('product_id', 'Product:' );
		$product->setDefaultValue( $this->product_id );
		$form->addField($product);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->product_id = (int)$form->field('product_id')->getValue();
		if($this->product_id) {
			$this->listing->setParam('product', $this->product_id);
		} else {
			$this->listing->unsetParam('product');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->product_id) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'product_id'   => $this->product_id,
		]);
	}
	
}