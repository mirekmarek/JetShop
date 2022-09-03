<?php
namespace JetShop;

use Jet\Form;

abstract class Core_Property_Number extends Property
{
	protected string $type = Property::PROPERTY_TYPE_NUMBER;

	protected function generateAddForm() : Form
	{
		$form = parent::generateAddForm();

		$form->removeField('stencil_id');

		foreach( Shops::getList() as $shop ) {
			$form->removeField('/shop_data/'.$shop->getKey().'/bool_yes_description');
		}

		return $form;
	}

	protected function generateEditForm() : Form
	{
		$form = parent::generateEditForm();

		$form->removeField('stencil_id');

		foreach( Shops::getList() as $shop ) {
			$form->removeField('/shop_data/'.$shop->getKey().'/bool_yes_description');
		}

		return $form;
	}

	public function getValueInstance() : Property_Value_Number
	{
		return new Property_Value_Number( $this );
	}

	public function getFilterInstance( ProductListing $listing ) : ProductListing_Filter_Params_Property_Number
	{
		return new ProductListing_Filter_Params_Property_Number( $listing, $this );
	}
	
}