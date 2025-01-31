<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use JetApplication\EShopEntity_HasActivation_Interface;

interface Core_EShopEntity_HasActivationByTimePlan_Interface extends EShopEntity_HasActivation_Interface {
	
	public function setActiveFrom( Data_DateTime|string|null $value ): void;
	public function getActiveFrom(): Data_DateTime|null;
	public function setActiveTill( Data_DateTime|string|null $value ): void;
	public function getActiveTill(): Data_DateTime|null;
	public function hasTimePlan() : bool;
	public function isActiveByTimePlan() : bool;
	public function isExpiredByTimePlan() : bool;
	public function isWaitingByTimePlan() : bool;
	public static function handleTimePlan() : void;
	
}