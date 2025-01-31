<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\SetProductPrices;



use Jet\Logger;
use Jet\Tr;
use JetApplication\Brand;
use JetApplication\DataExport_XLSX;
use JetApplication\Pricelist;
use JetApplication\Product;
use JetApplication\Product_Price;
use JetApplication\Product_EShopData;
use JetApplication\EShops;
use JetApplication\Supplier;
use XLSXReader\XLSXReader;

class ProductPriceList {
	
	protected string $product_identifier;
	protected Pricelist $pricelist;
	protected ?int $brand_id = null;
	protected ?int $supplier_id = null;
	
	/**
	 * @var ProductPriceList_Item[]
	 */
	protected array $items = [];
	
	
	public function __construct(
		string    $product_identifier,
		Pricelist $pricelist,
		?int      $brand_id=null,
		?int      $supplier_id=null
	)
	{
		$this->product_identifier = $product_identifier;
		$this->pricelist = $pricelist;
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
		$where = EShops::getCurrent()->getWhere();
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
		
		
		$data = Product_EShopData::dataFetchAll(
			select: [
				'id' => 'entity_id',
				'ean',
				'internal_code',
				'name'
			],
			where: $where
		);
		
		$product_ids = [];
		
		foreach($data as $d) {
			$product_ids[] = $d['id'];
		}
		
		
		if($product_ids) {
			Product_Price::prefetch( $this->pricelist, $product_ids );
		}
		
		
		foreach($data as $d) {
			$price = Product_Price::get($this->pricelist, $d['id']);
			$d['price'] = $price->getPrice();
			$d['vat_rate'] = $price->getVatRate();
			
			$item = new ProductPriceList_Item($d, $this->product_identifier);
			$this->items[] = $item;
		}
		
	}

	public function getPricelist() : Pricelist
	{
		return $this->pricelist;
	}
	
	
	/**
	 * @return ProductPriceList_Item[]
	 */
	public function getItems() : array
	{
		return $this->items;
	}
	
	public function export() : void
	{
		
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
		
		$xlsx = new DataExport_XLSX(
			header: $export_header,
			data: $data
		);
		
		$xlsx->setSheetName( 'PriceList' );
		
		
		
		if($this->brand_id) {
			$name = Brand::getScope()[$this->brand_id];
		} else {
			$name = Supplier::getScope()[$this->supplier_id];
		}
		
		$name .= '_'.$this->pricelist->getCode();
		
		$file_name = 'Price_list_'.$name.'_' . date( 'Y-m-d_Hi' ) . '.xlsx';
		
		$xlsx->sentToBeDownloaded( $file_name );

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
			
			
			$pp = Product_Price::get( $this->pricelist, $item->getId() );
			$pp->setPrice( $new_price );
			$pp->save();
			
			Logger::warning(
				event:'price_updated',
				event_message: 'Product ['.$item->getId().']['.$this->pricelist->getCode().'] price changer '.$item->getPrice().' > '.$item->getNewPrice(),
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