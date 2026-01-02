<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\MarketplaceIntegration;
use JetApplication\EShops;


class Listing_Filter_Marketplace extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'marketplace';
	protected string $label = 'Marketplace';
	
	protected function getOptions() : array
	{
		return MarketplaceIntegration::getScope();
	}
	
	public function generateWhere(): void
	{
		if($this->value=='') {
			return;
		}
		
		[$mp_code, $eshop_key] = explode(':', $this->value);
		
		$eshop = EShops::get( $eshop_key );
		
		$mp = MarketplaceIntegration::getActiveModule( $mp_code );
		$ids = $mp->getSellingProductIds(  $eshop);
		if(!$ids) {
			$ids = [0];
		}
		
		$this->listing->addFilterWhere([
			'id'   => $ids,
		]);
	}
	
}