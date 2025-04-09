<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Payment\Methods;


use JetApplication\Delivery_Method;
use Jet\DataListing_Column;
use Jet\Tr;
use JetApplication\Pricelists;

class Listing_Column_Price extends DataListing_Column
{
	public const KEY = 'price';
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+payment_methods_price.price';
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-payment_methods_price.price';
	}
	
	public function getTitle(): string
	{
		return Tr::_('Price');
	}
	
	public function getExportHeader(): null|string|array
	{
		$headers = [];
		
		foreach(Pricelists::getList() as $pricelist) {
			$headers[$pricelist->getCode()] = 'Price - '.$pricelist->getName();
		}
		
		return $headers;
	}
	
	public function getExportData( mixed $item ): float|int|bool|string|array
	{
		/**
		 * @var Delivery_Method $item
		 */
		$data = [];
		
		foreach(Pricelists::getList() as $pricelist) {
			$data[$pricelist->getCode()] = $item->getPrice( $pricelist );
		}
		
		return $data;
	}
}