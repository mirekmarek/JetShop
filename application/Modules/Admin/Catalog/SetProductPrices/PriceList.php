<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Catalog\SetProductPrices;


use Jet\Application;
use Jet\Http_Headers;
use Jet\Logger;
use Jet\Tr;
use JetApplication\Brand;
use JetApplication\Product;
use JetApplication\Product_ShopData;
use JetApplication\Shops_Shop;
use JetApplicationModule\Admin\Suppliers\Supplier;
use XLSXReader\XLSXReader;
use XLSXWriter\XLSXWriter;

class PriceList {
	
	protected string $product_identifier;
	protected Shops_Shop $shop;
	protected ?int $brand_id = null;
	protected ?int $supplier_id = null;
	
	/**
	 * @var PriceList_Item[]
	 */
	protected array $items = [];
	
	
	public function __construct( string $product_identifier, Shops_Shop $shop, ?int $brand_id=null, ?int $supplier_id=null )
	{
		$this->product_identifier = $product_identifier;
		$this->shop = $shop;
		$this->brand_id = $brand_id;
		$this->supplier_id = $supplier_id;
		
		$this->read();
	}
	
	public static function getIdentifiers() : array
	{
		return [
			'id'            => Tr::_('ID'),
			'internal_code' => Tr::_('Internal code'),
			'ean'           => Tr::_('EAN'),
		];
	}
	
	public function read() : void
	{
		$where = $this->shop->getWhere();
		$where[] = 'AND';
		if($this->brand_id) {
			$where['brand_id'] = $this->brand_id;
		} else {
			$where['supplier_id'] = $this->supplier_id;
		}
		
		$where[] = 'AND';
		$where['type'] = [
			Product::PRODUCT_TYPE_VARIANT,
			Product::PRODUCT_TYPE_REGULAR
		];
		
		$data = Product_ShopData::dataFetchAll(
			select: [
				'id' => 'entity_id',
				'ean',
				'internal_code',
				
				'vat_rate',
				'name',
				'price'
			],
			where: $where
		);
		
		foreach($data as $d) {
			$item = new PriceList_Item($d, $this->product_identifier);
			$this->items[] = $item;
		}
		
	}
	
	public function getShop(): Shops_Shop
	{
		return $this->shop;
	}
	
	
	
	/**
	 * @return PriceList_Item[]
	 */
	public function getItems() : array
	{
		return $this->items;
	}
	
	public function export() : void
	{
		if($this->brand_id) {
			$name = Brand::getScope()[$this->brand_id];
		} else {
			$name = Supplier::getScope()[$this->brand_id];
		}
		
		$sheet_name = 'PriceList';
		
		$export_header = [
			static::getIdentifiers()[$this->product_identifier],
			Tr::_('Price'),
			Tr::_('VAT rate'),
			Tr::_('Name')
		];
		$data = [];
		
		foreach($this->getItems() as $item) {
			$l = [];
			$l[] = $item->getProductIdentification();
			$l[] = $item->getPrice();
			$l[] = $item->getVatRate();
			$l[] = $item->getName();
			
			$data[] = $l;
		}
		
		
		$xls = new XLSXWriter();
		
		
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
		
		
		$file_name = 'Price_list_'.$name.'_' . date( 'Y-m-d_Hi' ) . '.xlsx';
		
		$res = $xls->writeToString();
		
		Http_Headers::sendDownloadFileHeaders(
			file_name: $file_name,
			file_mime: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			file_size: strlen($res),
			force_download: true
		);
		
		echo $res;
		
		
		
		Application::end();
	}
	
	public function import( string $file_path ) : void
	{
		$xlsx = new XLSXReader( $file_path );
		
		$price_list_data = $xlsx->getSheetData( array_values($xlsx->getSheetNames())[0] );
		unset($price_list_data[0]);
		
		foreach($this->getItems() as $item) {
			
			$id = $item->getProductIdentification();
			$new_item = null;
			
			foreach( $price_list_data as $pd ) {
				if( $pd[0] == $id ) {
					$new_item = $pd;
					break;
				}
			}
			
			if($new_item) {
				$item->setNewPrice( $new_item[1] );
			}
		}
	}
	
	public function setNewPrices() : void
	{
		foreach($this->items as $item) {
			$new_price = $item->getNewPrice();
			if(
				!$new_price ||
				$new_price==$item->getPrice()
			) {
				continue;
			}
			
			$product = Product_ShopData::get( $item->getId(), $this->shop );
			if(!$product) {
				continue;
			}
			
			
			$product->setPrice( $new_price );
			$product->actualizePriceReferences();
			
			Logger::warning(
				event:'price_updated',
				event_message: 'Product ['.$item->getId().'] price changer '.$item->getPrice().' > '.$item->getNewPrice(),
				context_object_id: $item->getId(),
				context_object_data: [
					'product_id'=> $item->getId(),
					'old_price' => $item->getPrice(),
					'new_price' => $item->getNewPrice()
				]
			);
			
		}
		
	}
}