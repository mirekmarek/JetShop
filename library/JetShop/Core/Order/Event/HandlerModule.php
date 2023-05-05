<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\Mailing;

use JetApplication\Order_Event;
use JetApplication\Shops_Shop;
use JetApplication\Order;
use JetApplication\Order_Notification_Email;
use JetApplication\Order_Notification_SMS;


abstract class Core_Order_Event_HandlerModule extends Application_Module
{
	protected Order_Event $event;
	protected Shops_Shop $shop;
	protected Order $order;

	/**
	 * @var Order_Notification_Email[]|Order_Notification_SMS[]
	 */
	protected ?array $notifications = null;


	public function init( Order_Event $event )
	{
		$this->event = $event;
		$order = $event->getOrder();
		$this->shop = $order->getShop();
		$this->order = $order;
	}

	public function getEvent(): Order_Event
	{
		return $this->event;
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
		$shop = $this->event->getShop();

		$email = new Order_Notification_Email();
		$email->setViewRootDir( $this->getViewsDir().$kind.'/email/'.$shop->getLocale().'/' );
		$email->setKind( $kind );
		$email->setShop( $shop );
		$email->setCustomerId( $this->event->getOrder()->getCustomerId() );
		$email->setOrderId( $this->event->getOrder()->getId() );
		$email->setMailTo( $this->order->getEmail() );


		$email->setViewData('event', $this);
		$email->setViewData('order', $this->order);

		$sender = Mailing::getConfig()->getSender( Mailing::DEFAULT_SENDER_ID );
		$email->setSenderName( $sender->getName() );
		$email->setSenderEmail( $sender->getEmail() );

		return $email;
	}

	protected function prepareSms( string $kind ) : Order_Notification_SMS
	{
		$shop = $this->event->getShop();

		$SMS = new Order_Notification_SMS();
		$SMS->setViewRootDir( $this->getViewsDir().$kind.'/SMS/'.$shop->getLocale().'/' );
		$SMS->setKind($kind);
		$SMS->setShop( $shop );
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