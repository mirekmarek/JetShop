<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\MarketplaceIntegration;

class Listing_Filter_Source extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'source';
	
	protected string $label = 'Order source';
	
	protected function getOptions() : array
	{
		return [
				'eshop' => 'e-shop'
			] + MarketplaceIntegration::getSourcesScope();
	}
	
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'import_source'   => $this->value!='eshop'? $this->value : '',
		]);
	}
	
}