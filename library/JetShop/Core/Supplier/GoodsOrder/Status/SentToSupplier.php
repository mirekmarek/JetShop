<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use JetApplication\Supplier_GoodsOrder_Status;

abstract class Core_Supplier_GoodsOrder_Status_SentToSupplier extends Supplier_GoodsOrder_Status {
	
	public const CODE = 'sent_to_supplier';
	protected bool $sent_to_the_cupplier = true;
	
	public function __construct()
	{
		$this->title = Tr::_('Sent to the supplier', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 60;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}