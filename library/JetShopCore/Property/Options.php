<?php
namespace JetShop;
use Jet\Form;

abstract class Core_Property_Options extends Property
{
	protected string $type = Property::PROPERTY_TYPE_OPTIONS;

	protected function generateAddForm() : Form
	{
		$form = parent::generateAddForm();

		$form->removeField('decimal_places');
		$form->removeField('stencil_id');

		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();

			$form->removeField('/shop_data/'.$shop_key.'/bool_yes_description');
		}

		return $form;
	}

	protected function generateEditForm() : Form
	{
		$form = parent::generateEditForm();

		$form->removeField('decimal_places');
		$form->removeField('stencil_id');

		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();

			$form->removeField('/shop_data/'.$shop_key.'/bool_yes_description');
		}

		return $form;
	}

	public function getValueInstance() : Property_Value_Options
	{
		return new Property_Value_Options( $this );
	}

	public function getFilterInstance( ProductListing $listing ) : ProductListing_Filter_Properties_Property_Options
	{
		return new ProductListing_Filter_Properties_Property_Options( $listing, $this );
	}

}