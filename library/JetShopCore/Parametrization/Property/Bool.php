<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\Form;


abstract class Core_Parametrization_Property_Bool extends Parametrization_Property
{
	protected string $type = Parametrization_Property::PROPERTY_TYPE_BOOL;

	protected function generateAddForm() : Form
	{
		$form = parent::generateAddForm();

		$form->removeField('decimal_places');
		$form->removeField('stencil_id');

		return $form;
	}


	protected function generateEditForm() : Form
	{
		$form = parent::generateEditForm();

		$form->removeField('decimal_places');
		$form->removeField('stencil_id');

		return $form;
	}

	public function getValueInstance() : Parametrization_Property_Value_Bool
	{
		return new Parametrization_Property_Value_Bool( $this );
	}

	public function getFilterInstance( ProductListing $listing ) : ProductListing_Filter_Properties_Property_Bool
	{
		return new ProductListing_Filter_Properties_Property_Bool( $listing, $this );
	}

}