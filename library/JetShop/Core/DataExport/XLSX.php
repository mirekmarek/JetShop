<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application;
use Jet\Data_DateTime;
use Jet\Http_Headers;
use Jet\Locale;
use Jet\Tr;
use XLSXWriter\XLSXWriter;

abstract class Core_DataExport_XLSX {
	
	protected array $header;
	protected array $data;
	protected string $sheet_name = 'Sheet1';
	
	public function __construct( array $header, array $data )
	{
		$this->header = $header;
		$this->data = $data;
	}
	
	public function getSheetName(): string
	{
		return $this->sheet_name;
	}
	
	public function setSheetName( string $sheet_name ): void
	{
		$this->sheet_name = $sheet_name;
	}
	
	public function getHeader(): array
	{
		return $this->header;
	}
	
	public function setHeader( array $header ): void
	{
		$this->header = $header;
	}
	
	public function getData(): array
	{
		return $this->data;
	}
	
	public function setData( array $data ): void
	{
		$this->data = $data;
	}
	
	
	
	public function create() : string
	{
		$sheet_name = $this->sheet_name;
		$export_header = $this->header;
		$data = $this->data;
		
		
		
		$str_lens = [];
		
		foreach( $export_header as $i=>$header ) {
			$i++;
			$len = strlen( (string)$header )+2;
			$str_lens[$i] = $len;
		}
		
		foreach( $data as $d ) {
			foreach( $d as $i => $v ) {
				$i++;
				$len = strlen( (string)$v )+2;
				
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
			$widths[] = $str_lens[$i]??0 + 20;
		}
		
		$xls = new XLSXWriter();
		
		$xls->writeSheetHeader( $sheet_name, $header_types, [
			'widths' => $widths,
			'suppress_row' => true
		] );
		$xls->writeSheetRow( $sheet_name, $export_header, $header_style );
		
		
		$count = count( $data );
		$i = 0;
		
		foreach( $data as $item ) {
			$i++;
			
			foreach( $item as $v_i=>$v ) {
				if($v instanceof Data_DateTime) {
					if($v->isOnlyDate()) {
						$item[$v_i] = Locale::date( $v );
					} else {
						$item[$v_i] = Locale::dateAndTime( $v );
					}
				}
				
				if( is_bool($v) ) {
					$item[$v_i] = Tr::_( $v?'Yes':'No', dictionary: Tr::COMMON_DICTIONARY );
				}
				
				if(is_string($v)) {
					$v = str_replace("\r\n", "\n", $v);
					$v = str_replace("\n", "\r\n", $v);
					$item[$v_i] = $v;
				}
			}
			
			$xls->writeSheetRow( $sheet_name, $item, ($i == $count) ? $last_row_style : $row_style );
		}
		
		
		
		return $xls->writeToString();
	}
	
	public function sentToBeDownloaded( string $file_name ) : void
	{
		
		$res = $this->create();
		
		Http_Headers::sendDownloadFileHeaders(
			file_name: $file_name,
			file_mime: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			file_size: strlen($res),
			force_download: true
		);
		
		echo $res;
		
		Application::end();
		
	}
}