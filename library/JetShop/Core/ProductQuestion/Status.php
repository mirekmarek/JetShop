<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\ProductQuestion;
use JetApplication\ProductQuestion_Event;
use JetApplication\ProductQuestion_Status;

abstract class Core_ProductQuestion_Status extends EShopEntity_Status {
	
	protected static string $base_status_class = ProductQuestion_Status::class;
	
	protected static array $flags_map = [];
	
	protected static ?array $list = null;
	
	abstract public function createEvent( EShopEntity_Basic|ProductQuestion $item, EShopEntity_Status $previouse_status ): ?ProductQuestion_Event;
	
}