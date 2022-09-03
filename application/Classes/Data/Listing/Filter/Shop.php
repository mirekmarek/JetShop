<?php
namespace JetShop;

use Jet\Data_Listing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;

class Data_Listing_Filter_Shop extends Data_Listing_Filter{
	
	protected string $shop = '';
	
	public function catchGetParams(): void
	{
		$this->shop = Http_Request::GET()->getString('shop');
		$this->listing->setGetParam('shop', $this->shop);
	}
	
	public function generateFormFields( Form $form ): void
	{
		$field = new Form_Field_Select('shop', 'Shop:' );
		$field->setDefaultValue( $this->shop );
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
	
	public function catchForm( Form $form ): void
	{
		$value = $form->field('shop')->getValue();
		
		$this->shop = $value;
		$this->listing->setGetParam('shop', $value);
	}
	
	public function generateWhere(): void
	{
		if($this->shop) {
			$this->listing->addWhere(Shops::get($this->shop)->getWhere());
		}
	}
}