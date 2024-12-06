<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\ReturnOfGoods\Updated;

use JetApplication\ReturnOfGoods;
use JetApplication\EMail_TemplateProvider;
use JetApplication\ReturnOfGoods_Event_HandlerModule;

/**
 *
 */
class Main extends ReturnOfGoods_Event_HandlerModule implements EMail_TemplateProvider
{
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		return true;
	}
	
	public function sendReturnOfGoodsUpdatedEmail( ReturnOfGoods $return_of_goods ) : bool
	{
		$template = new EMailTemplate();
		$template->setReturnOfGoods( $return_of_goods );
		$email = $template->createEmail( $return_of_goods->getEshop() );
		
		return $email->send();
	}
	
	
	public function sendNotifications(): bool
	{
		return $this->sendEMail( new EMailTemplate() );
	}
	
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Return of goods updated';
	}
	
	public function getEventStyle(): string
	{
		return 'color: #ffffff;background-color: #808080;';
	}
}