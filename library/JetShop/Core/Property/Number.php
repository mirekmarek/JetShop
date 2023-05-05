<?php
namespace JetShop;

use Jet\Form;

use JetApplication\Property;
use JetApplication\Shops;
use JetApplication\Property_Number_Value;
use JetApplication\ProductListing;
use JetApplication\Property_Number_Filter;


abstract class Core_Property_Number extends Property
{
	protected string $type = Property::PROPERTY_TYPE_NUMBER;

	protected function generateAddForm() : Form
	{
		$form = parent::generateAddForm();

		foreach( Shops::getList() as $shop ) {
			$form->removeField('/shop_data/'.$shop->getKey().'/bool_yes_description');
		}

		return $form;
	}

	protected function generateEditForm() : Form
	{
		$form = parent::generateEditForm();

		foreach( Shops::getList() as $shop ) {
			$form->removeField('/shop_data/'.$shop->getKey().'/bool_yes_description');
		}

		return $form;
	}

	public function getValueInstance() : Property_Number_Value
	{
		return new Property_Number_Value( $this );
	}
	
	public function initFilter( ProductListing $listing ): void
	{
		$this->filter = new Property_Number_Filter( $listing, $this );
	}
	
	public function filter() : Property_Number_Filter
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filter;
	}
	
}