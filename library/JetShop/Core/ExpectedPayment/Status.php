<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\ExpectedPayment;
use JetApplication\ExpectedPayment_Event;
use JetApplication\ExpectedPayment_Status;

abstract class Core_ExpectedPayment_Status extends EShopEntity_Status {
	
	protected static string $base_status_class = ExpectedPayment_Status::class;
	
	protected static array $flags_map = [
		'cancelled' => null,
	];
	
	protected static ?array $list = null;
	
	abstract public function createEvent( EShopEntity_Basic|ExpectedPayment $item, EShopEntity_Status $previouse_status ): ?ExpectedPayment_Event;
}