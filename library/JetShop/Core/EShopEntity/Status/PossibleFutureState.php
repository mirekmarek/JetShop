<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\BaseObject;
use Jet\Tr;
use Jet\UI_button;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_VirtualStatus;

abstract class Core_EShopEntity_Status_PossibleFutureState extends BaseObject {
	
	abstract public function getButton() : UI_button;
	
	abstract public function getStatus() : EShopEntity_Status|EShopEntity_VirtualStatus;
	
	public function internalNoteEnabled(): bool
	{
		return true;
	}
	
	public function noteForCustomerEnabled(): bool
	{
		return false;
	}
	
	public function doNotHandleEventSwitchEnabled() : bool
	{
		return false;
	}
	
	public function doNotHandleEventSwitchLabel() : string
	{
		return Tr::_('Do not handle event');
	}
	
	public function doNotHandleExternalsSwitchEnabled() : bool
	{
		return false;
	}
	
	public function doNotHandleExternalsSwitchLabel() : string
	{
		return Tr::_('Do not set external status');
	}
	
	
	public function doNotSendNotificationsSwitchEnabled() : bool
	{
		return false;
	}
	
	public function doNotSendNotificationsSwitchLabel() : string
	{
		return Tr::_('Do not send notifications to the customer');
	}
	
}