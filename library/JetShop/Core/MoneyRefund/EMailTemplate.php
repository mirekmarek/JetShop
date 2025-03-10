<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\EShop;

abstract class Core_MoneyRefund_EMailTemplate extends EMail_Template {
	
	protected MoneyRefund $money_refund;
	protected MoneyRefund_Event $event;
	
	public function getMoneyRefund(): MoneyRefund
	{
		return $this->money_refund;
	}
	
	public function setMoneyRefund( MoneyRefund $money_refund ): void
	{
		$this->money_refund = $money_refund;
	}
	
	public function getEvent(): MoneyRefund_Event
	{
		return $this->event;
	}
	
	public function setEvent( MoneyRefund_Event $event ): void
	{
		$this->event = $event;
		$this->money_refund = $event->getMoneyRefund();
	}
	
	
	
	public function initTest( EShop $eshop ): void
	{
		$ids = MoneyRefund::dataFetchCol(
			select: ['id'],
			where: $eshop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->money_refund = MoneyRefund::get($id);
	}
	
	public function setupEMail( EShop $eshop, EMail $email ): void
	{
		$email->setContext( MoneyRefund::getEntityType() );
		$email->setContextId( $this->money_refund->getId() );
		$email->setContextCustomerId( $this->money_refund->getCustomerId() );
		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->money_refund->getEmail() );
	}
	
	protected function initCommonProperties() : void
	{
		$code_property = $this->addProperty('money_refund_number', Tr::_('Money refund number'));
		$code_property->setPropertyValueCreator( function() : string {
			return $this->money_refund->getNumber();
		} );
		
		
	}
	
}