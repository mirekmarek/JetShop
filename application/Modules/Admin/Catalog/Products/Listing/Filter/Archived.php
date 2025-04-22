<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;

use JetApplication\Admin_Listing_Filter_StdFilter_YesNo;


class Listing_Filter_Archived extends Admin_Listing_Filter_StdFilter_YesNo
{
	public const KEY = 'archived';
	protected string $label = 'Archived';
	
	public function generateWhere(): void
	{
		if($this->value=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'archived'   => $this->value==static::YES,
		]);
	}
	
}