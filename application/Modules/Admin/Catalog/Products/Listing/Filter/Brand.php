<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Brand;


class Listing_Filter_Brand extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'brand';
	protected string $label = 'Brand';
	
	protected function getOptions() : array
	{
		return Brand::getScope();
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'brand_id'   => $this->value,
		]);
	}
}