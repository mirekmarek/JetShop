<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\VirtualProductHandler\Vouchers;

use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\EShop;
use JetApplication\Order;

class EMailTemplate extends EMail_Template {
	
	protected Order $order;
	protected string $pdf;
	
	protected function init(): void
	{
		$this->setInternalName('Dárkový poukaz');
		$this->setInternalNotes('');
	}
	
	public function initTest( EShop $eshop ): void
	{
	}
	
	
	protected function initCommonFields(): void
	{
	
	}
	
	public function getOrder(): Order
	{
		return $this->order;
	}
	
	public function setOrder( Order $order ): void
	{
		$this->order = $order;
	}
	
	public function getPdf(): string
	{
		return $this->pdf;
	}
	
	public function setPdf( string $pdf ): void
	{
		$this->pdf = $pdf;
	}
	
	
	
	public function setupEMail( EShop $eshop, EMail $email ) : void
	{

		$email->setContext( Order::getEntityType() );
		$email->setContextId( $this->order->getId() );
		$email->setContextCustomerId( $this->order->getCustomerId() );
		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->order->getEmail() );
		
		$email->addAttachmentsData(
			'voucher.pdf',
			'application/pdf',
			$this->pdf,
		);
	}
	
}