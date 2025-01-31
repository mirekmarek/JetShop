<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_MinimalOrderAmount extends DataListing_Column
{
	public const KEY = 'minimal_order_amount';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Minimal order amount' );
	}
}

