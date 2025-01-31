<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Discounts\CodesDefinition;


use Jet\DataListing_Column;
use Jet\Tr;

class Listing_Column_InternalNotes extends DataListing_Column
{
	public const KEY = 'internal_notes';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( 'Internal notes' );
	}
}