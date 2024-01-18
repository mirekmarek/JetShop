<?php
/** @noinspection ALL */
/** @noinspection PhpMissingFieldTypeInspection */

namespace XLSXReader;

/*
XLSXReader
Greg Neustaetter <gneustaetter@gmail.com>
Artistic License
*/

use ZipArchive;
use Exception;

class XLSXReader_XLSXWorksheet {
	
	/**
	 * @var XLSXReader
	 */
	protected $workbook;
	
	/**
	 * @var string
	 */
	public $sheetName = '';
	
	/**
	 * @var array
	 */
	protected $data;
	
	/**
	 * @var int
	 */
	public $colCount;
	
	/**
	 * @var int
	 */
	public $rowCount;
	
	/**
	 * @var array
	 */
	public $cell_fill = [];
	
	/**
	 * @var array
	 */
	protected $config;
	
	public function __construct($xml, $sheetName, XLSXReader $workbook) {
		$this->config = $workbook->config;
		$this->sheetName = $sheetName;
		$this->workbook = $workbook;
		$this->parse($xml);
	}
	
	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * @param $xml
	 */
	protected function parse($xml) {
		$this->parseDimensions($xml->dimension);
		$this->parseData($xml->sheetData);
	}
	
	protected function parseDimensions($dimensions) {
		$range = (string) $dimensions['ref'];
		$cells = explode(':', $range);
		
		if(isset($cells[1])) {
			$maxValues = $this->getColumnIndex($cells[1]);
			$this->colCount = $maxValues[0] + 1;
			$this->rowCount = $maxValues[1] + 1;
		} else {
			$this->colCount = 25;
			$this->rowCount = 999;
			
		}
		
		if($this->colCount>25) {
			$this->colCount = 25;
		}
	}
	
	protected function parseData($sheetData) {
		$rows = array();
		$curR = 0;
		$lastDataRow = -1;
		foreach ($sheetData->row as $row) {
			
			if( ((string)$row['hidden'])=='true' ) {
				continue;
			}
			
			$rowNum = (int)$row['r'];
			if($rowNum>4096) {
				break;
			}
			
			$this->cell_fill[$rowNum] = [];
			
			if($rowNum != ($curR + 1)) {
				$missingRows = $rowNum - ($curR + 1);
				for($i=0; $i < $missingRows; $i++) {
					$rows[$curR] = array_pad(array(),$this->colCount,null);
					$curR++;
				}
			}
			$curC = 0;
			$rowData = array();
			foreach ($row->c as $c) {
				
				[$cellIndex,] = $this->getColumnIndex((string) $c['r']);
				
				$this->cell_fill[$rowNum][$cellIndex] = $this->getColumnFill( $c );
				
				if($cellIndex !== $curC) {
					$missingCols = $cellIndex - $curC;
					for($i=0;$i<$missingCols;$i++) {
						$rowData[$curC] = null;
						$curC++;
					}
				}
				$val = $this->parseCellValue($c);
				if(!is_null($val)) {
					$lastDataRow = $curR;
				}
				
				$rowData[$curC] = $val;
				$curC++;
			}
			
			$rows[$curR] = array_pad($rowData, $this->colCount, null);
			$curR++;
		}
		
		
		if($this->config['removeTrailingRows']) {
			$this->data = array_slice($rows, 0, $lastDataRow + 1);
			$this->rowCount = count($this->data);
		} else {
			$this->data = $rows;
		}
	}
	
	/**
	 * @param $cell
	 * @return string
	 */
	protected function getColumnFill( $cell )
	{
		if(!isset($this->workbook->styles[(string)$cell['s']])) {
			return '';
		}
		
		$style = $this->workbook->styles[(string)$cell['s']];
		
		if( !isset($style['fillId']) ) {
			return '';
		}
		
		if(!isset($this->workbook->styles_cells_fills[$style['fillId']])) {
			return '';
		}
		
		$fill = $this->workbook->styles_cells_fills[$style['fillId']];
		
		return $fill;
		
	}
	
	protected function getColumnIndex($cell = 'A1') {
		if (preg_match("/([A-Z]+)(\d+)/", $cell, $matches)) {
			
			$col = $matches[1];
			$row = $matches[2];
			$colLen = strlen($col);
			$index = 0;
			
			for ($i = $colLen-1; $i >= 0; $i--) {
				$index += (ord($col[$i]) - 64) * pow(26, $colLen-$i-1);
			}
			return array($index-1, $row-1);
		}
		throw new Exception("Invalid cell index");
	}
	
	protected function parseCellValue($cell) {
		// $cell['t'] is the cell type
		switch ((string)$cell["t"]) {
			case "s": // Value is a shared string
				if ((string)$cell->v != '') {
					$value = $this->workbook->sharedStrings[intval($cell->v)];
				} else {
					$value = '';
				}
				break;
			case "b": // Value is boolean
				$value = (string)$cell->v;
				if ($value == '0') {
					$value = false;
				} else if ($value == '1') {
					$value = true;
				} else {
					$value = (bool)$cell->v;
				}
				break;
			case "inlineStr": // Value is rich text inline
				$value = self::parseRichText($cell->is);
				break;
			case "e": // Value is an error message
				if ((string)$cell->v != '') {
					$value = (string)$cell->v;
				} else {
					$value = '';
				}
				break;
			default:
				if(!isset($cell->v)) {
					return null;
				}
				$value = (string)$cell->v;
				
				// Check for numeric values
				if (is_numeric($value)) {
					if ($value == (int)$value) $value = (int)$value;
					elseif ($value == (float)$value) $value = (float)$value;
					elseif ($value == (double)$value) $value = (double)$value;
				}
		}
		return $value;
	}
	
	// returns the text content from a rich text or inline string field
	public static function parseRichText($is = null) {
		$value = array();
		if (isset($is->t)) {
			$value[] = (string)$is->t;
		} else {
			foreach ($is->r as $run) {
				$value[] = (string)$run->t;
			}
		}
		return implode(' ', $value);
	}
}
