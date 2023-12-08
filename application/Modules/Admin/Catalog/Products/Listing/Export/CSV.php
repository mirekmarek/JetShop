<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing_Export_CSV;
use Jet\Tr;

class Listing_Export_CSV extends DataListing_Export_CSV
{
	public function getTitle() : string
	{
		return Tr::_('to CSV');
	}
	
	public function getKey() : string
	{
		return 'csv';
	}
	
	protected function generateFileName(): string
	{
		return 'products_' . date( 'YmdHis' ) . '.csv';
	}
}