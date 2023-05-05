<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Http_Request;

/**
 *
 */
trait Controller_Main_Listing_Export
{
	public function export_Action() : void
	{
		$listing = $this->getListing();
		
		$types = array_keys( $listing->getExportTypes() );
		
		$listing->export(
			Http_Request::GET()->getString(
				key: 'export',
				default_value: $types[0],
				valid_values: $types
			)
		);
	}
}