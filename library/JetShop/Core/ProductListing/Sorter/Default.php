<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Category_Product;
use JetApplication\ProductListing_Sorter;

abstract class Core_ProductListing_Sorter_Default extends ProductListing_Sorter
{
	
	
	public function getKey(): string
	{
		return 'default';
	}
	
	public function getLabel(): string
	{
		return Tr::_('Default');
	}
	
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
		
		return array_keys($map);
	}
}