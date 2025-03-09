<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event_Rollback;
use JetApplication\MoneyRefund_Status_New;
use JetApplication\MoneyRefund_VirtualStatus;

abstract class Core_MoneyRefund_VirtualStatus_Rollback extends MoneyRefund_VirtualStatus
{
	public const CODE = 'rollback';
	
	public static function handle( EShopEntity_HasStatus_Interface|MoneyRefund $item ): void
	{
		$item->setStatus( MoneyRefund_Status_New::get(), handle_event: false );
		$item->initEvent( MoneyRefund_Event_Rollback::new() )->handleImmediately();
	}
}