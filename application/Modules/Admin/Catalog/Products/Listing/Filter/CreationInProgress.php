<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;

use JetApplication\Admin_Listing_Filter_StdFilter_YesNo;


class Listing_Filter_CreationInProgress extends Admin_Listing_Filter_StdFilter_YesNo
{
	public const KEY = 'creation_in_progress';
	protected string $label = 'Creation in progress';

	
	public function generateWhere(): void
	{
		if($this->value=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'creation_in_progress'   => $this->value==static::YES,
		]);
	}
	
}