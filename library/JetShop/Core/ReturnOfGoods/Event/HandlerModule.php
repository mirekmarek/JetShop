<?php
/**
 *
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\ReturnOfGoods_EMailTemplate;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\EShop;
use JetApplication\ReturnOfGoods;


abstract class Core_ReturnOfGoods_Event_HandlerModule extends Event_HandlerModule
{
	protected ReturnOfGoods_Event $event;
	protected EShop $eshop;
	protected ReturnOfGoods $return_of_goods;


	public function init( EShopEntity_Event $event ) : void
	{
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->event = $event;
		$return = $event->getReturnOfGoods();
		$this->eshop = $return->getEshop();
		$this->return_of_goods = $return;
	}

	public function getEvent(): ReturnOfGoods_Event
	{
		return $this->event;
	}

	public function getReturnOfGoods(): ReturnOfGoods
	{
		return $this->return_of_goods;
	}
	
	public function sendEMail( ReturnOfGoods_EMailTemplate $template ) : bool
	{
		$template->setEvent($this->event);
		$email = $template->createEmail( $this->getEvent()->getEshop() );
		
		return $email->send();
	}

}