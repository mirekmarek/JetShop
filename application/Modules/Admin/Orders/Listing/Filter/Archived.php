<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Data_DateTime;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Order;

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
		
		Order::updateData(
			data: [
				'archived' => true,
			],
			where: [
				'archived' => false,
				'AND',
				'date_purchased <=' => new Data_DateTime( date('Y-m-d H:i:s', strtotime('-2 years')) ),
			]
		);
	}
	
}