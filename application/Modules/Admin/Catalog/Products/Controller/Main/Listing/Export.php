<?php
namespace JetShopModule\Admin\Catalog\Products;


use Jet\Http_Request;

/**
 *
 */
trait Controller_Main_Listing_Export
{
	public function export_Action() : void
	{
		$types = array_keys(Listing::getExportTypes());
		
		$listing = $this->getListing();
		$listing->export(
			Http_Request::GET()->getString(
				key: 'export',
				default_value: $types[0],
				valid_values: $types
			)
		);
	}
}