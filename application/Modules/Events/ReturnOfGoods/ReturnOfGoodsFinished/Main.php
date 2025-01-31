<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\ReturnOfGoods\ReturnOfGoodsFinished;


use JetApplication\EMail_TemplateProvider;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event_HandlerModule;


class Main extends ReturnOfGoods_Event_HandlerModule implements EMail_TemplateProvider
{
	public function getEMailTemplates(): array
	{
		return [
			(new EMailTemplate())
		];
	}
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return $this->sendNewReturnOfGoodsEmail( $this->getReturnOfGoods() );
	}
	
	public function sendNewReturnOfGoodsEmail( ReturnOfGoods $rog ) : bool
	{
		$email_template = new EMailTemplate();
		
		$email_template->setReturnOfGoods( $rog );
		
		$email = $email_template->createEmail( $rog->getEshop() );
		
		return $email->send();
		
	}
	
	public function getEventNameReadable(): string
	{
		return 'New finished return of goods';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}