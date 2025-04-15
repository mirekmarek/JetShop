<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use JetApplication\Supplier_GoodsOrder_Status;

abstract class Core_Supplier_GoodsOrder_Status_GoodsReceived extends Supplier_GoodsOrder_Status {
	
	public const CODE = 'goods_received';
	
	protected bool $cancel_allowed = false;
	
	protected bool $goods_received = true;
	
	public function __construct()
	{
		$this->title = Tr::_('Goods received', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 70;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}