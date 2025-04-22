<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use JetApplication\Admin_Listing_Filter_StdFilter_YesNo;


class Listing_Filter_IsFilter extends Admin_Listing_Filter_StdFilter_YesNo
{
	public const KEY = 'is_filter';
	protected string $label = 'Is filter';
	
	
	public function generateWhere(): void
	{
		if($this->value=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'is_filter'   => $this->value==static::YES
		]);
	}
	
}