<?php
/** @noinspection ALL */
/** @noinspection PhpMissingFieldTypeInspection */

namespace XLSXReader;
/*
XLSXReader
Greg Neustaetter <gneustaetter@gmail.com>
Artistic License

XLSXReader is a heavily modified version of:
	SimpleXLSX php class v0.4 (Artistic License)
	Created by Sergey Schuchkin from http://www.sibvision.ru - professional php developers team 2010-2011
	Downloadable here: http://www.phpclasses.org/package/6279-PHP-Parse-and-retrieve-data-from-Excel-XLS-files.html

Key Changes include:
	Separation into two classes - one for the Workbook and one for Worksheets
	Access to sheets by name or sheet id
	Use of ZIP extension
	On-demand access of files inside zip
	On-demand access to sheet data
	No storage of XML objects or XML text
	When parsing rows, include empty rows and null cells so that data array has same number of elements for each row
	Configuration option for removing trailing empty rows
	Better handling of cells with style information but no value
	Change of class names and method names
	Removed rowsEx functionality including extraction of hyperlinks
*/

use ZipArchive;
use Exception;

class XLSXReader {
	const SCHEMA_OFFICEDOCUMENT  =  'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument';
	const SCHEMA_RELATIONSHIP  =  'http://schemas.openxmlformats.org/package/2006/relationships';
	const SCHEMA_OFFICEDOCUMENT_RELATIONSHIP = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
	const SCHEMA_SHAREDSTRINGS =  'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings';
	const SCHEMA_WORKSHEETRELATION =  'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet';
	const SCHEMA_STYLES =  'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles';
	
	/**
	 * @var array
	 */
	protected $sheets = [];
	
	/**
	 * @var array
	 */
	public $sharedStrings = [];
	
	/**
	 * @var array
	 */
	public $styles = [];
	
	/**
	 * @var array
	 */
	public $styles_cells_fills = [];
	
	/**
	 * @var
	 */
	protected $sheetInfo;
	
	/**
	 * @var ZipArchive
	 */
	protected $zip;
	
	/**
	 * @var array
	 */
	public $config = array(
		'removeTrailingRows' => true
	);
	
	
	/**
	 * @param string $filePath
	 *
	 * @throws Exception
	 */
	public function __construct( $filePath ) {
		
		$this->zip = new ZipArchive();
		$status = $this->zip->open($filePath);
		if($status === true) {
			$this->parse();
		} else {
			throw new Exception("Failed to open $filePath with zip error code: $status");
		}
		
	}
	
	// get a file from the zip
	protected function readXmlFromDocument( $name ) {
		
		$data = $this->zip->getFromName($name);
		if($data === false) {
			throw new Exception("File $name does not exist in the Excel file");
		} else {
			$data = str_replace('<x:', '<', $data);
			$data = str_replace('</x:', '</', $data);
			
			return $data;
		}
	}
	
	// extract the shared string and the list of sheets
	protected function parse() {
		$sheets = array();
		$relationshipsXML = simplexml_load_string($this->readXmlFromDocument("_rels/.rels"));
		
		foreach($relationshipsXML->Relationship as $rel) {
			
			if($rel['Type'] == self::SCHEMA_OFFICEDOCUMENT) {
				$workbookDir = dirname($rel['Target']) . '/';
				
				$workbookXML = simplexml_load_string($this->readXmlFromDocument( $rel['Target']));
				
				foreach($workbookXML->sheets->sheet as $sheet) {
					$r = $sheet->attributes('r', true);
					$sheets[(string)$r->id] = array(
						'sheetId' => (int)$sheet['sheetId'],
						'name' => (string)$sheet['name']
					);
				}
				
				$workbookRelationsXML = simplexml_load_string($this->readXmlFromDocument($workbookDir . '_rels/' . basename( $rel['Target']) . '.rels'));
				foreach($workbookRelationsXML->Relationship as $wrel) {
					switch($wrel['Type']) {
						case self::SCHEMA_WORKSHEETRELATION:
							$sheets[(string)$wrel['Id']]['path'] = $workbookDir . (string)$wrel['Target'];
							break;
						case self::SCHEMA_SHAREDSTRINGS:
							$sharedStringsXML = simplexml_load_string($this->readXmlFromDocument($workbookDir . (string)$wrel['Target']));
							foreach($sharedStringsXML->si as $val) {
								if(isset($val->t)) {
									$this->sharedStrings[] = (string)$val->t;
								} elseif(isset($val->r)) {
									$this->sharedStrings[] = XLSXReader_XLSXWorksheet::parseRichText($val);
								}
							}
							break;
						case self::SCHEMA_STYLES:
							
							$styles_xml = simplexml_load_string($this->readXmlFromDocument($workbookDir . (string)$wrel['Target']));
							foreach($styles_xml->cellXfs->xf as $val) {
								
								$style = [];
								foreach( $val->attributes() as $k=>$v ) {
									$style[$k] = (int)$v;
								}
								
								$this->styles[] = $style;
							}
							
							foreach( $styles_xml->fills->fill as $_fill ) {
								$fill_color = '';
								
								if($_fill->patternFill && $_fill->patternFill['patternType']) {
									switch($_fill->patternFill['patternType']) {
										case 'none':
											break;
										case 'solid':
											$fill_color = 'solid:'.$_fill->patternFill->bgColor['indexed'];
											break;
										default:
											$fill_color = (string)$_fill->patternFill['patternType'];
											break;
									}
								}
								
								$this->styles_cells_fills[] = $fill_color;
								
								
							}
							
							break;
					}
				}
			}
		}
		
		$this->sheetInfo = array();
		foreach($sheets as $rid=>$info) {
			$this->sheetInfo[(string)$info['name']] = array(
				'sheetId' => $info['sheetId'],
				'rid' => $rid,
				'path' => $info['path']
			);
		}
		
	}
	
	/**
	 * @return array
	 */
	public function getSheetNames() {
		$res = array();
		
		foreach($this->sheetInfo as $sheetName=>$info) {
			$res[$info['sheetId']] = $sheetName;
		}
		
		return $res;
	}
	
	/**
	 * @return int
	 */
	public function getSheetCount() {
		return count($this->sheetInfo);
	}
	
	/**
	 * @param string $sheetNameOrId
	 * @return array
	 *
	 * @throws Exception
	 */
	public function getSheetData($sheetNameOrId) {
		$sheet = $this->getSheet($sheetNameOrId);
		return $sheet->getData();
	}
	
	/**
	 * @param string $sheet
	 *
	 * @return XLSXReader_XLSXWorksheet
	 *
	 * @throws Exception
	 */
	public function getSheet($sheet) {
		if(is_numeric($sheet)) {
			$sheet = $this->getSheetNameById($sheet);
		} elseif(!is_string($sheet)) {
			throw new Exception("Sheet must be a string or a sheet Id");
		}
		
		if(!array_key_exists($sheet, $this->sheets)) {
			$this->sheets[$sheet] = new XLSXReader_XLSXWorksheet($this->getSheetXML($sheet), $sheet, $this);
			
		}
		
		return $this->sheets[$sheet];
	}
	
	/**
	 * @param $sheetId
	 * @return int|string
	 * @throws Exception
	 */
	public function getSheetNameById($sheetId) {
		foreach($this->sheetInfo as $sheetName=>$sheetInfo) {
			if($sheetInfo['sheetId'] === $sheetId) {
				return $sheetName;
			}
		}
		throw new Exception("Sheet ID $sheetId does not exist in the Excel file");
	}
	
	/**
	 * @param $name
	 *
	 * @return \SimpleXMLElement
	 *
	 * @throws Exception
	 */
	protected function getSheetXML($name) {
		return simplexml_load_string($this->readXmlFromDocument( $this->sheetInfo[$name]['path']));
	}
	
	/**
	 * @param float|int $excelDateTime
	 * @return float|int
	 */
	public static function toUnixTimeStamp($excelDateTime) {
		if(!is_numeric($excelDateTime)) {
			return $excelDateTime;
		}
		$d = floor($excelDateTime); // seconds since 1900
		$t = $excelDateTime - $d;
		return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
	}
	
}

