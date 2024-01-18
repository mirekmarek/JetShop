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

abstract class Core_ProductListing_Sorter_ByName extends ProductListing_Sorter
{
	
	
	public function getKey(): string
	{
		return 'by_name';
	}
	
	public function getLabel(): string
	{
		return Tr::_('By name');
	}
	
	public function sort( array $product_ids ): array
	{
		return Product_ShopData::dataFetchCol(
			select: ['entity_id'],
			where: [
				'entity_id' => $product_ids,
			],
			order_by: ['name'],
			raw_mode: true
		);
	}
}