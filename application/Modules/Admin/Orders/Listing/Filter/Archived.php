<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Tr;
use JetApplication\Admin_Listing_Filter_StdFilter;

class Listing_Filter_Archived extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'archived';
	protected string $label = 'Archived';
	
	protected function getOptions() : array
	{
		return [
			'' => Tr::_('Only non-archived orders'),
			'all' => Tr::_('All orders'),
		];
	}
	
	protected function _getOptions() : array
	{
		return $this->getOptions();
	}
	
	
	public function generateWhere(): void
	{
		if($this->value=='all') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'archived' => false,
		]);
		
	}
	
}