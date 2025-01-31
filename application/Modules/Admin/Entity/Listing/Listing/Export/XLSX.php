<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\DataListing_Export;
use Jet\Tr;
use JetApplication\DataExport_XLSX;

class Listing_Export_XLSX extends DataListing_Export
{
	public function getTitle() : string
	{
		return Tr::_('XLSX', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	public function getKey() : string
	{
		return 'xlsx';
	}
	
	protected function formatData( array $export_header, array $data ): void
	{
		$sheet_name = 'Sheet1';
		
		$xlsx = new DataExport_XLSX(
			header: $export_header,
			data: $data
		);
		$xlsx->setSheetName('Sheet1');
		
		$file_name = 'export_' . date( 'YmdHis' ) . '.xlsx';
		
		$xlsx->sentToBeDownloaded( $file_name );
	}
}