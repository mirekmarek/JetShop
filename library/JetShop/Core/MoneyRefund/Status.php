<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event;
use JetApplication\MoneyRefund_Status;

abstract class Core_MoneyRefund_Status extends EShopEntity_Status {
	
	protected static string $base_status_class = MoneyRefund_Status::class;
	
	protected static array $flags_map = [];
	
	protected static ?array $list = null;
	
	public function createEvent( EShopEntity_Basic|MoneyRefund $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|MoneyRefund_Event
	{
		return null;
	}
	
}