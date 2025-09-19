<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\PaymentFeeDiscount;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\Payment_Method;
use JetApplication\Marketing_PaymentFeeDiscount;

class Listing_Column_PaymentMethod extends Admin_Listing_Column
{
	public const KEY = 'payment_method';
	
	public function getTitle(): string
	{
		return Tr::_('Payment method');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		$column->addCustomCssStyle('width:200px;');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Marketing_PaymentFeeDiscount $item
		 */
		return Payment_Method::getScope()[$item->getPaymentMethodId()]??'';
	}
}