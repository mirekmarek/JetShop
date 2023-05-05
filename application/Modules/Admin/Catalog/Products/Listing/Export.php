<?php

namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Application;
use Jet\BaseObject;
use JetApplication\Product;

abstract class Listing_Export extends BaseObject
{
	const XLSX = 'xlsx';
	const CSV = 'csv';
	
	const LIMIT = 500;
	
	protected Listing $listing;
	
	public function __construct( Listing $listing )
	{
		$this->listing = $listing;
	}
	
	
	
	public function export(): void
	{
		$columns = $this->listing->getVisibleColumns();
		
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
		
		$ids = $this->listing->getAllIds();
		if( count( $ids ) > static::LIMIT ) {
			$ids = array_slice( $ids, 0, static::LIMIT );
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
		
		$this->formatData( $export_header, $data );
		
		Application::end();
	}
	
	abstract public function getTitle() : string;
	
	abstract public function getKey() : string;
	
	abstract protected function formatData( array $export_header, array $data ): void;

}