<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Complaint\GoodsReceived;


use Jet\Data_DateTime;
use JetApplication\Complaint_Event_HandlerModule;
use JetApplication\EMail_TemplateProvider;


class Main extends Complaint_Event_HandlerModule implements EMail_TemplateProvider
{
	
	
	public function sendNotifications(): bool
	{
		return $this->sendEMail( new EMailTemplate() );
	}
	
	public function getEMailTemplates(): array
	{
		$email_template = new EMailTemplate();
		
		return [
			$email_template
		];
	}
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		$this->complaint->setDateOfReceiptOfClainedGoods( Data_DateTime::now() );
		$this->complaint->save();
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Goods received';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-dispatched';
	}
}