<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\PromoAreas;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Marketing_PromoAreaDefinition;


class Listing_Filter_Area extends Admin_Listing_Filter_StdFilter
{
	
	public const KEY = 'area';
	protected string $label = 'Area';

	protected function getOptions() : array
	{
		return Marketing_PromoAreaDefinition::getScope();
	}
	
	public function generateWhere(): void
	{
		if( $this->value ) {
			$this->listing->addFilterWhere( [
				'promo_area_id' => $this->value,
			] );
		}
	}
	
}