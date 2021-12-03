<?php
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;

trait Data_Listing_Filter_shop {
	protected string $shop = '';

	/**
	 *
	 */
	protected function filter_shop_catchGetParams() : void
	{
		$this->shop = Http_Request::GET()->getString('shop');
		$this->setGetParam('shop', $this->shop);
	}

	/**
	 * @param Form $form
	 */
	public function filter_shop_catchForm( Form $form ) : void
	{
		$value = $form->field('shop')->getValue();

		$this->shop = $value;
		$this->setGetParam('shop', $value);
	}

	/**
	 * @param Form $form
	 */
	protected function filter_shop_getForm( Form $form ) : void
	{
		$field = new Form_Field_Select('shop', 'Shop:', $this->shop);
		$field->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => ' '
		]);
		$options = [0=>Tr::_('- all -')];

		foreach(Shops::getList() as $shop) {
			$options[$shop->getKey()] = $shop->getShopName();
		}
		$field->setSelectOptions( $options );


		$form->addField($field);
	}

	/**
	 *
	 */
	protected function filter_shop_getWhere() : void
	{
		if($this->shop) {
			$this->filter_addWhere(Shops::get($this->shop)->getWhere());
		}
	}

}