<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Order\Events\NewOrderSave;

use JetApplication\Order_Event_HandlerModule;

/**
 *
 */
class Main extends Order_Event_HandlerModule
{

	public function setExternalStatus(): bool
	{
		return true;
	}

	public function setStatus(): bool
	{
		return true;
	}


	public function generateNotifications(): array
	{
		$notifications = [];

		$email = $this->prepareEmail('new_order_confirmation');
		$notifications[] = $email;

		$SMS = $this->prepareSms('new_order_confirmation');
		$notifications[] = $SMS;

		return $notifications;
	}

}