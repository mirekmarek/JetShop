<?php
namespace JetShop;

use Jet\DataModel_Definition;

/**
 *
 */
#[DataModel_Definition]
class Parametrization_Property extends Core_Parametrization_Property {

	public function getValueInstance() : Parametrization_Property_Value|null
	{
		return null;
	}


	public function getFilterInstance( ProductListing $listing ) : ProductListing_Filter_Properties_Property|null
	{
		return null;
	}

}