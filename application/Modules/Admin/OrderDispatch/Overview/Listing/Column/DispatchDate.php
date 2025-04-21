<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;

class Listing_Column_DispatchDate extends Admin_Listing_Column
{
	public const KEY = 'dispatch_date';
	
	public function getTitle(): string
	{
		return Tr::_('Day of dispatch');
	}
}