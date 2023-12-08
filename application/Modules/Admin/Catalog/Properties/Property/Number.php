<?php
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\Form;
use JetApplication\ProductListing;
use JetApplication\Property_Filter;
use JetApplication\Property_Value;
use JetApplication\Shops;

/**
 *
 */
class Property_Number extends Property
{
	public function __construct()
	{
		parent::__construct();
		$this->type = Property::PROPERTY_TYPE_NUMBER;
	}
	
	protected function _generateAddForm( Form $form ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$form->removeField('/shop_data/'.$shop->getKey().'/bool_yes_description');
		}
	}
	
	protected function _generateEditForm( Form $form ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$form->removeField('/shop_data/'.$shop->getKey().'/bool_yes_description');
		}
	}
	
	public function getValueInstance(): Property_Value|null
	{
		return null;
	}
	
	public function initFilter( ProductListing $listing ): void
	{
	}
	
	public function filter(): ?Property_Filter
	{
		return null;
	}
}