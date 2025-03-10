<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\MoneyRefund_Event;

interface Core_EShopEntity_HasEvents_Interface {
	
	public function createEvent( EShopEntity_Event $event ) : EShopEntity_Event;
	
	/**
	 * @return MoneyRefund_Event[]
	 */
	public function getHistory() : array;

}