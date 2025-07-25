<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Product_EShopData;
use JetApplication\ProductListing_Sorter;

abstract class Core_ProductListing_Sorter_ByName extends ProductListing_Sorter
{
	
	protected static string $key = 'by_name';
	protected string $label = 'By name';
	
	public function sort( array $product_ids ): array
	{
		$where = $this->listing->getEshop()->getWhere();
		
		return Product_EShopData::dataFetchCol(
			select: ['entity_id'],
			where: [
				$where,
				'AND',
				'entity_id' => $product_ids,
			],
			order_by: ['name'],
			raw_mode: true
		);
	}
}