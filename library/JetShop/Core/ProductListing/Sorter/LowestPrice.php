<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product_Price;
use JetApplication\ProductListing_Sorter;

abstract class Core_ProductListing_Sorter_LowestPrice extends ProductListing_Sorter
{
	protected static string $key = 'lowest_price';
	protected string $label = 'Lowest price';
	
	public function sort( array $product_ids ): array
	{
		return Product_Price::orderAsc( $this->listing->getPricelist(), $product_ids );
	}
}