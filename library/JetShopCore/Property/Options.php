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

		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();

			$form->removeField('/shop_data/'.$shop_key.'/bool_yes_description');
		}

		return $form;
	}

	public function getValueInstance() : Property_Options_Value
	{
		return new Property_Options_Value( $this );
	}
	
	public function initFilter( ProductListing $listing ): void
	{
		$this->filter = new Property_Options_Filter( $listing, $this );
		
		foreach($this->options as $option) {
			$option->initFilter( $listing, $this );
		}
	}
	
	public function filter() : Property_Options_Filter
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filter;
	}

}