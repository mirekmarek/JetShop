<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\Form;

abstract class Core_Parametrization_Property_Number extends Parametrization_Property
{
	protected string $type = Parametrization_Property::PROPERTY_TYPE_NUMBER;

	protected function generateAddForm() : Form
	{
		$form = parent::generateAddForm();

		$form->removeField('stencil_id');

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$form->removeField('/shop_data/'.$shop_code.'/bool_yes_description');
		}

		return $form;
	}

	protected function generateEditForm() : Form
	{
		$form = parent::generateEditForm();

		$form->removeField('stencil_id');

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$form->removeField('/shop_data/'.$shop_code.'/bool_yes_description');
		}

		return $form;
	}

	public function getValueInstance() : Parametrization_Property_Value_Number
	{
		return new Parametrization_Property_Value_Number( $this );
	}

	public function getFilterInstance( ProductListing $listing ) : ProductListing_Filter_Properties_Property_Number
	{
		return new ProductListing_Filter_Properties_Property_Number( $listing, $this );
	}
	
}