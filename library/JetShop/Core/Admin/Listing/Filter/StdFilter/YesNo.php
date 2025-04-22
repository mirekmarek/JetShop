<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use JetApplication\Admin_Listing_Filter_StdFilter;

abstract class Core_Admin_Listing_Filter_StdFilter_YesNo extends Admin_Listing_Filter_StdFilter
{
	protected const YES = 'yes';
	protected const NO = 'no';
	
	protected function getOptions(): array
	{
		return [
			static::YES => Tr::_( 'yes', dictionary: Tr::COMMON_DICTIONARY ),
			static::NO  => Tr::_( 'no',  dictionary: Tr::COMMON_DICTIONARY ),
		];
	}
}