<?php
namespace JetApplicationModule\Admin\Catalog\Products;

class Listing_Export_CSV extends Listing_Export
{
	public function getTitle() : string
	{
		return 'to CSV';
	}
	
	public function getKey() : string
	{
		return 'csv';
	}
	
	protected function formatData( array $export_header, array $data ): void
	{
		$file_name = 'products_' . date( 'YmdHis' ) . '.csv';
		
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