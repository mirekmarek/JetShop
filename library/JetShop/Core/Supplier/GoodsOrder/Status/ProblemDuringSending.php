<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use JetApplication\Supplier_GoodsOrder_Status;

abstract class Core_Supplier_GoodsOrder_Status_ProblemDuringSending extends Supplier_GoodsOrder_Status {
	
	public const CODE = 'problem_during_sending';
	
	protected bool $order_can_be_updated = true;
	protected bool $send_allowed = true;
	
	public function __construct()
	{
		$this->title = Tr::_('Problem during sending', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 65;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-warning';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}