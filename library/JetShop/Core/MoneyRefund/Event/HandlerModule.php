<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\MoneyRefund_Event;
use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\EShop;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_EMailTemplate;


abstract class Core_MoneyRefund_Event_HandlerModule extends Event_HandlerModule
{
	protected MoneyRefund_Event $event;
	protected EShop $eshop;
	protected MoneyRefund $money_refund;


	public function init( EShopEntity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$money_refund = $event->getMoneyRefund();
		$this->eshop = $money_refund->getEshop();
		$this->money_refund = $money_refund;
	}

	public function getEvent(): MoneyRefund_Event
	{
		return $this->event;
	}

	public function getMoneyRefund(): MoneyRefund
	{
		return $this->money_refund;
	}
	
	public function sendEMail( MoneyRefund_EMailTemplate $template ) : bool
	{
		$template->setEvent($this->event);
		$email = $template->createEmail( $this->getEvent()->getEshop() );
		
		return $email->send();
	}
}