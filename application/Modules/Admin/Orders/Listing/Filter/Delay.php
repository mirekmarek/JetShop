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

class Listing_Filter_Delay extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'delay';
	
	protected string $label = 'Delay';
	
	protected function getOptions(): array
	{
		return [
			'1' => Tr::_('Older than 1 day'),
			'2' => Tr::_('Older than 2 days'),
			'3' => Tr::_('Order than 3 days'),
			'4' => Tr::_('Order than 4 days'),
			'5' => Tr::_('Order than 5 days'),
			'6' => Tr::_('Order than 6 days'),
			'7' => Tr::_('Order than 7 days'),
			'8' => Tr::_('Order than 8 days'),
			'9' => Tr::_('Order than 9 days'),
			'10' => Tr::_('Order than 10 days'),
		];
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		
		$date = new Data_DateTime( date('Y-m-d 00:00:00', strtotime('-'.$this->value.' days')) );
		
		$where = [
			'date_purchased < ' => $date,
			'AND',
			'cancelled' => false,
			'AND',
			'dispatched' => false,
			'AND',
			'delivered' => false,
			'AND',
			'returned' => false
		];
		
		$this->listing->addFilterWhere( $where );
		
	}
	
}