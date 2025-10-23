<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Category_Product;
use JetApplication\Product_Availability;
use JetApplication\ProductListing_Sorter;

abstract class Core_ProductListing_Sorter_Default extends ProductListing_Sorter
{
	protected static string $key = 'default';
	protected string $label = 'Default';
	
	
	public function sort( array $product_ids ): array
	{
		if( !($category_id=$this->listing->getCategoryId()) ) {
			return $product_ids;
		}
		
		$priorities = Category_Product::dataFetchAssoc(
			select: ['product_id','priority'],
			where: [
				'category_id' => $this->listing->getCategoryId(),
				'AND',
				'product_id' => $product_ids,
			],
			order_by: ['priority'],
			raw_mode: true
		);
		
		$map = [];
		foreach($product_ids as $id) {
			$map[$id] = $priorities[$id]['priority']??9999;
		}
		
		asort( $map );
		
		
		$product_ids = array_keys($map);
		
		$avl = $this->listing->getAvailability();
		
		$avl_products = Product_Availability::filterIsInStock( $avl, true, $product_ids );
		
		$res = [];
		
		foreach($product_ids as $id) {
			if(in_array($id, $avl_products)) {
				$res[] = $id;
			}
		}
		foreach($product_ids as $id) {
			if(!in_array($id, $avl_products)) {
				$res[] = $id;
			}
		}
		
		
		return $res;
	}
}