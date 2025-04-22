<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Property;

class Listing_Filter_Type extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'type';
	
	protected string $label = 'Type';
	
	protected function getOptions(): array
	{
		return Property::getTypesScope();
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