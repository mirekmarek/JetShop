<?php
namespace JetShop;

use Jet\Form;


abstract class Core_Property_Bool extends Property
{
	protected string $type = Property::PROPERTY_TYPE_BOOL;

	protected function generateAddForm() : Form
	{
		$form = parent::generateAddForm();

		$form->removeField('decimal_places');

		return $form;
	}


	protected function generateEditForm() : Form
	{
		$form = parent::generateEditForm();

		$form->removeField('decimal_places');

		return $form;
	}

	public function getValueInstance() : Property_Bool_Value
	{
		return new Property_Bool_Value( $this );
	}
	
	public function initFilter( ProductListing $listing ): void
	{
		$this->filter = new Property_Bool_Filter( $listing, $this );
	}
	
	public function filter() : Property_Bool_Filter
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filter;
	}

}