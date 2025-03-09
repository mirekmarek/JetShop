<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\BaseObject;
use Jet\UI_button;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_VirtualStatus;

abstract class Core_EShopEntity_Status_PossibleFutureState extends BaseObject {
	
	abstract public function getButton() : UI_button;
	
	abstract public function getStatus() : EShopEntity_Status|EShopEntity_VirtualStatus;
}