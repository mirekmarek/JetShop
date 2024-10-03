<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\DataListing_Column;
use Jet\Tr;
use Jet\UI_dataGrid_column;

class Listing_Column_FreeLimit extends DataListing_Column {
	
	public function getKey(): string
	{
		return 'payment_free_limit';
	}
	
	public function getTitle(): string
	{
		return Tr::_('Payment free limit');
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:250px');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
}