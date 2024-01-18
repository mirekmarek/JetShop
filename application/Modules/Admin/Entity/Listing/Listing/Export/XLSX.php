<?php
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\DataListing_Export;
use Jet\Tr;
use XLSXWriter\XLSXWriter;

class Listing_Export_XLSX extends DataListing_Export
{
	public function getTitle() : string
	{
		return Tr::_('XLSX');
	}
	
	public function getKey() : string
	{
		return 'xlsx';
	}
	
	protected function formatData( array $export_header, array $data ): void
	{
		$sheet_name = 'Sheet1';
		
		$xls = new XLSXWriter();
		
		//$xls->writeSheetHeader($sheet_name, ['string','string','string','string'], ['widths'=>[30,60,18,40], 'suppress_row'=>true] );
		
		$str_lens = [];
		
		foreach( $data as $d ) {
			foreach( $d as $i => $v ) {
				$i++;
				$len = strlen( (string)$v );
				
				if( !isset( $str_lens[$i] ) ) {
					$str_lens[$i] = $len;
				} else {
					if( $len > $str_lens[$i] ) {
						$str_lens[$i] = $len;
					}
				}
			}
		}
		
		
		$header_style = [];
		$row_style = [];
		$last_row_style = [];
		$header_types = [];
		
		$count = count( $export_header );
		$i = 0;
		$widths = [];
		foreach( $export_header as $header ) {
			$i++;
			$header_style[] = $i == $count
				? [
					'font-style' => 'bold',
					'border'     => 'bottom,left,right,top',
					'halign'     => 'center'
				]
				: [
					'font-style' => 'bold',
					'border'     => 'bottom,left,top',
					'halign'     => 'center'
				];
			$row_style[] = $i == $count ? ['border' => 'left,right'] : ['border' => 'left'];
			$last_row_style[] = $i == $count ? ['border' => 'bottom,left,right'] : ['border' => 'bottom,left'];
			$header_types[] = 'string';
			$widths[] = $str_lens[$i] + 2;
		}
		
		$xls->writeSheetHeader( $sheet_name, $header_types, [
			'widths' => $widths,
			'suppress_row' => true
		] );
		$xls->writeSheetRow( $sheet_name, $export_header, $header_style );
		
		
		$count = count( $data );
		$i = 0;
		
		foreach( $data as $item ) {
			$i++;
			
			$xls->writeSheetRow( $sheet_name, $item, ($i == $count) ? $last_row_style : $row_style );
		}
		
		
		$file_name = 'products_' . date( 'YmdHis' ) . '.xlsx';
		
		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment;filename="' . $file_name . '"' );
		header( 'Cache-Control: max-age=0' );
		
		$xls->writeToStdOut();
		
	}
}