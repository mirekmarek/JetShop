<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;

use Jet\Tr;
use JetApplication\Admin_Listing_Filter_StdFilter;

class Listing_Filter_Status extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'status';
	protected string $label = 'Status';
	
	protected function getOptions() : array
	{
		return [
			'cancelled' => Tr::_('Cancelled'),
			'active' => Tr::_('Active'),
		];
	}
	
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'cancelled'   => $this->value==='cancelled',
		]);
	}
	
}