<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\Mailing;

abstract class Core_Order_Event_HandlerModule extends Application_Module
{
	protected Order_Event $event;

	protected string $shop_code;

	protected Order $order;

	/**
	 * @var Order_Notification_Email[]|Order_Notification_SMS[]
	 */
	protected ?array $notifications = null;


	public function init( Order_Event $event )
	{
		$this->event = $event;
		$order = $event->getOrder();

		$this->shop_code = $order->getShopCode();
		$this->order = $order;
	}

	public function getEvent(): Order_Event
	{
		return $this->event;
	}

	public function getShopCode(): string
	{
		return $this->shop_code;
	}

	public function getOrder(): Order
	{
		return $this->order;
	}


	public function handle() : bool
	{
		$e = $this->event;

		$e->setErrorMessage('');

		if(
			!$e->getExternalStatusSet() &&
			!$e->getDoNotSetExternalStatus()
		) {
			if($this->setExternalStatus()) {
				$e->setExternalStatusSet( true );
			}
		}

		if(
			(
				$e->getExternalStatusSet() ||
				$e->getDoNotSetExternalStatus()
			) &&
			!$e->getNotificationSent() &&
			!$e->getDoNotSendNotification()
		) {
			if($this->sendNotifications()) {
				$e->setNotificationSent( true );
			}
		}

		if(
			(
				$e->getNotificationSent() ||
				$e->getDoNotSendNotification()
			) &&
			!$e->getStatusSet()
		) {
			if($this->setStatus()) {
				$e->setStatusSet( true );
			}
		}

		$res = false;

		if(
			(
				$e->getExternalStatusSet() ||
				$e->getDoNotSetExternalStatus()
			) &&
			(
				$e->getNotificationSent() ||
				$e->getDoNotSendNotification()
			) &&
			$e->getStatusSet()
		) {
			$e->setHandled( true );
			$res = true;
		}

		$e->save();

		return $res;
	}

	/**
	 * @return Order_Notification_Email[]|Order_Notification_SMS[]
	 */
	public function getNotifications() : array
	{
		if($this->notifications===null) {
			$this->notifications = $this->generateNotifications();
		}

		return $this->notifications;
	}

	public function sendNotifications() : bool
	{
		foreach( $this->getNotifications() as $notification ) {
			$notification->send();
		}

		return true;
	}


	protected function prepareEmail( string $kind ) : Order_Notification_Email
	{
		$shop_code = $this->event->getShopCode();
		$shop = Shops::get($shop_code);

		$email = new Order_Notification_Email();
		$email->setViewRootDir( $this->getViewsDir().$kind.'/email/'.$shop->getLocale().'/' );
		$email->setKind( $kind );
		$email->setShopCode( $shop->getCode() );
		$email->setCustomerId( $this->event->getOrder()->getCustomerId() );
		$email->setOrderId( $this->event->getOrder()->getId() );
		$email->setMailTo( $this->order->getEmail() );


		$email->setViewData('event', $this);
		$email->setViewData('order', $this->order);

		$sender = Mailing::getConfig()->getSender( $shop->getLocale(), $shop->getSiteId(), '' );
		$email->setSenderName( $sender->getName() );
		$email->setSenderEmail( $sender->getEmail() );

		return $email;
	}

	protected function prepareSms( string $kind ) : Order_Notification_SMS
	{
		$shop_code = $this->event->getShopCode();
		$shop = Shops::get($shop_code);

		$SMS = new Order_Notification_SMS();
		$SMS->setViewRootDir( $this->getViewsDir().$kind.'/SMS/'.$shop->getLocale().'/' );
		$SMS->setKind($kind);
		$SMS->setShopCode( $shop->getCode() );
		$SMS->setCustomerId( $this->event->getOrder()->getCustomerId() );
		$SMS->setOrderId( $this->event->getOrder()->getId() );
		$SMS->setToNumber( $this->order->getPhone() );

		$SMS->setViewData('event', $this);
		$SMS->setViewData('order', $this->order);

		return $SMS;
	}


	abstract public function setExternalStatus() : bool;

	/**
	 *
	 * @return Order_Notification_Email[]|Order_Notification_SMS[]
	 */
	abstract public function generateNotifications() : array;

	abstract public function setStatus() : bool;


}