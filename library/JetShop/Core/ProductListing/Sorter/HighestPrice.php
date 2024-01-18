<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Product_ShopData;
use JetApplication\ProductListing_Sorter;

abstract class Core_ProductListing_Sorter_HighestPrice extends ProductListing_Sorter
{
	
	
	public function getKey(): string
	{
		return 'highest_price';
	}
	
	public function getLabel(): string
	{
		return Tr::_('Highest price');
	}
	
	public function sort( array $product_ids ): array
	{
		return Product_ShopData::dataFetchCol(
			select: ['entity_id'],
			where: [
				'entity_id' => $product_ids,
			],
			order_by: ['-price'],
			raw_mode: true
		);
	}
}