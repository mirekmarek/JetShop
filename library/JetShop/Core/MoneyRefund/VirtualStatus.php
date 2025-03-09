<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\MoneyRefund_VirtualStatus;

abstract class Core_MoneyRefund_VirtualStatus extends EShopEntity_VirtualStatus {

	protected static string $base_status_class = MoneyRefund_VirtualStatus::class;
}