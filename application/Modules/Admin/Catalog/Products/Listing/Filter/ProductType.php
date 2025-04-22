<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Product;

class Listing_Filter_ProductType extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'product_type';
	protected string $label = 'Product type';
	
	protected function getOptions(): array
	{
		return Product::getProductTypes();
	}
	
	public function generateWhere(): void
	{
		if($this->value=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'type'   => $this->value,
		]);
	}
	
}