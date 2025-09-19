<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\BaseObject;
use JetApplication\Application_Service_EShop;
use JetApplication\SMS_Sent;
use JetApplication\EShop;

abstract class Core_SMS extends BaseObject
{
	
	protected EShop $eshop;
	
	protected string $template_code = '';
	
	protected int $context_customer_id = 0;
	
	protected string $context = '';
	
	protected int $context_id = 0;
	
	protected bool $save_history_after_send = true;
	
	protected string $to_phone_number = '';
	
	protected string $text = '';
	
	
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function setEshop( EShop $eshop ): void
	{
		$this->eshop = $eshop;
	}
	
	public function getTemplateCode(): string
	{
		return $this->template_code;
	}
	
	public function setTemplateCode( string $template_code ): void
	{
		$this->template_code = $template_code;
	}
	
	public function getContextCustomerId(): int
	{
		return $this->context_customer_id;
	}
	
	public function setContextCustomerId( int $context_customer_id ): void
	{
		$this->context_customer_id = $context_customer_id;
	}
	
	public function getContext(): string
	{
		return $this->context;
	}
	
	public function setContext( string $context ): void
	{
		$this->context = $context;
	}
	
	public function getContextId(): int
	{
		return $this->context_id;
	}
	
	public function setContextId( int $context_id ): void
	{
		$this->context_id = $context_id;
	}
	
	public function isSaveHistoryAfterSend(): bool
	{
		return $this->save_history_after_send;
	}
	
	public function setSaveHistoryAfterSend( bool $save_history_after_send ): void
	{
		$this->save_history_after_send = $save_history_after_send;
	}
	
	public function getToPhoneNumber(): string
	{
		return $this->to_phone_number;
	}
	
	public function setToPhoneNumber( string $to_phone_number ): void
	{
		$this->to_phone_number = $to_phone_number;
	}
	
	
	
	public function getText(): string
	{
		return $this->text;
	}
	
	public function setText( string $text ): void
	{
		$this->text = $text;
	}
	
	
	public function send(): bool
	{
		
		$sender = $sender = Application_Service_EShop::SMSSender( $this->getEshop() );
		if(!$sender) {
			return false;
		}
		
		if(!$sender->sendSMS(
			$this->getEshop(),
			$this->getToPhoneNumber(),
			$this->getText()
		)) {
			return false;
		}
		
		if( $this->save_history_after_send ) {
			SMS_Sent::SMSSent( $this );
		}
		
		return true;
	}
	
}