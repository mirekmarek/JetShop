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
		
		$res = Category_Product::dataFetchCol(
			select: ['product_id'],
			where: [
				//'category_id' => $this->listing->getCategoryId(),
				//'AND',
				'product_id' => $product_ids,
			],
			order_by: ['priority'],
			raw_mode: true
		);
		
		return $res;
	}
}