<?php

namespace JetShopModule\Admin\Catalog\Products;

use Jet\Application;
use Jet\Tr;
use JetShop\Product;
use JetShop\XLSXWriter;

trait Listing_Export
{
	
	protected static array $export_types = [
		self::EXPORT_TYPE_XLSX => 'To XLSX',
		self::EXPORT_TYPE_CSV  => 'To CSV',
	];
	
	
	public static function getExportTypes(): array
	{
		$types = [];
		foreach( static::$export_types as $type => $label ) {
			$types[$type] = Tr::_( $label );
		}
		
		return $types;
	}
	
	public function export( string $type ): void
	{
		$columns = $this->getVisibleColumns();
		
		$export_columns = [];
		$export_header = [];
		foreach( $columns as $col ) {
			$col_header = $col->getExportHeader();
			if( $col_header === null ) {
				continue;
			}
			
			if( is_array( $col_header ) ) {
				$export_columns[] = [$col];
				
				foreach( $col_header as $_col_header ) {
					$export_header[] = $_col_header;
				}
			} else {
				$export_header[] = $col_header;
				
				$export_columns[] = $col;
			}
		}
		
		$ids = $this->getAllIds();
		if( count( $ids ) > static::EXPORT_LIMIT ) {
			$ids = array_slice( $ids, 0, static::EXPORT_LIMIT );
		}
		
		$data = [];
		foreach( $ids as $id ) {
			$product = Product::get( $id );
			
			$data_row = [];
			foreach( $export_columns as $col ) {
				if( is_array( $col ) ) {
					foreach( $col[0]->getExportData( $product ) as $d ) {
						$data_row[] = $d;
					}
				} else {
					$data_row[] = $col->getExportData( $product );
				}
			}
			
			$data[] = $data_row;
		}
		
		switch( $type ) {
			case static::EXPORT_TYPE_XLSX:
				$this->export_XLSS( $export_header, $data );
				break;
			case static::EXPORT_TYPE_CSV:
				$this->export_CSV( $export_header, $data );
				break;
		}
		
		Application::end();
	}
	
	public function export_XLSS( $export_header, $data ): void
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
	
	public function export_CSV( $export_header, $data ): void
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