<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\Tr;
use JetApplication\Admin_Listing_Export;

class Listing_Export_CSV extends Admin_Listing_Export
{
	public function getTitle() : string
	{
		return Tr::_('CSV', dictionary: Tr::COMMON_DICTIONARY);
	}
	
	public function getKey() : string
	{
		return 'csv';
	}
	
	protected function formatData( array $export_header, array $data ): void
	{
		$file_name = 'export_' . date( 'YmdHis' ) . '.csv';
		
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment;filename="' . $file_name . '"' );
		header( 'Cache-Control: max-age=0' );
		
		$fp = fopen('php://output', 'w');
		
		fputcsv( $fp, $export_header );
		
		foreach( $data as $row ) {
			fputcsv( $fp, $row );
		}
		
		fclose( $fp );
		
	}
	
}