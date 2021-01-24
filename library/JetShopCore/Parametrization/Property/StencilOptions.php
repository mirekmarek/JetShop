<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field_Select;

abstract class Core_Parametrization_Property_StencilOptions extends Parametrization_Property
{
	protected string $type = Parametrization_Property::PROPERTY_TYPE_STENCIL_OPTIONS;

	protected Stencil|null $_stencil = null;

	protected function generateAddForm() : Form
	{
		$form = parent::generateAddForm();

		$form->removeField('decimal_places');
		$form->field('stencil_id')->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select stencil'
		]);

		foreach( Shops::getList() as $shop ) {
			$shop_id = $shop->getId();

			$form->removeField('/shop_data/'.$shop_id.'/bool_yes_description');
		}

		return $form;
	}

	protected function generateEditForm() : Form
	{
		$form = parent::generateEditForm();

		$form->removeField('decimal_places');
		$form->field('stencil_id')->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select stencil'
		]);

		foreach( Shops::getList() as $shop ) {
			$shop_id = $shop->getId();

			$form->removeField('/shop_data/'.$shop_id.'/bool_yes_description');
		}

		return $form;
	}

	public function getOption( int $id ) : Stencil_Option|null
	{
		if($this->_stencil===null) {
			$this->_stencil = Stencil::get($this->stencil_id);
		}

		if(!$this->_stencil) {
			return null;
		}

		return $this->_stencil->getOption( $id );
	}


	/**
	 * @return Stencil_Option[]
	 */
	public function getOptions() : iterable
	{
		if($this->_stencil===null) {
			$this->_stencil = Stencil::get($this->stencil_id);
		}

		if(!$this->_stencil) {
			return [];
		}

		return $this->_stencil->getOptions();
	}

	public function addOption( Parametrization_Property_Option $option ) : void
	{
	}

	public function getValueInstance() : Parametrization_Property_Value_StencilOptions
	{
		return new Parametrization_Property_Value_StencilOptions( $this );
	}

	public function getFilterInstance( ProductListing $listing ) : ProductListing_Filter_Properties_Property_StencilOptions
	{
		return new ProductListing_Filter_Properties_Property_StencilOptions( $listing, $this );
	}

}