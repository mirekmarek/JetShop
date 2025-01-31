<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\DataListing_Export_CSV;
use Jet\Tr;

class Listing_Export_CSV extends DataListing_Export_CSV
{
	public function getTitle() : string
	{
		return Tr::_('CSV', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	public function getKey() : string
	{
		return 'csv';
	}
	
	protected function generateFileName(): string
	{
		return 'export_' . date( 'YmdHis' ) . '.csv';
	}
}