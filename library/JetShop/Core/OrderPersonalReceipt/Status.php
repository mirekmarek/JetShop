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
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_Event;
use JetApplication\OrderPersonalReceipt_Status;

abstract class Core_OrderPersonalReceipt_Status extends EShopEntity_Status {
	
	protected static string $base_status_class = OrderPersonalReceipt_Status::class;
	
	
	protected static bool $is_editable = false;
	
	protected static array $flags_map = [];
	
	protected static ?array $list = null;
	
	public function createEvent( EShopEntity_Basic|OrderPersonalReceipt $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderPersonalReceipt_Event
	{
		return null;
	}
	
	public static function isEditable(): bool
	{
		return self::$is_editable;
	}
	
	
}