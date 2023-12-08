<?php
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\Form;
use JetApplication\ProductListing;
use JetApplication\Property_Filter;
use JetApplication\Property_Value;

/**
 *
 */
class Property_Bool extends Property
{
	public function __construct()
	{
		parent::__construct();
		$this->type = Property::PROPERTY_TYPE_BOOL;
	}
	
	protected function _generateAddForm( Form $form ) : void
	{
		$form->removeField('decimal_places');
	}
	
	
	protected function _generateEditForm( Form $form ) : void
	{
		$form->removeField('decimal_places');
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