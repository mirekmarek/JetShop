<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Events\Complaint\NewGoodsDispatched;


use JetApplication\Complaint_Event_HandlerModule;
use JetApplication\EMail_TemplateProvider;
use JetApplication\OrderDispatch;


class Main extends Complaint_Event_HandlerModule implements EMail_TemplateProvider
{
	
	public function sendNotifications(): bool
	{
		$email_template = new EMailTemplate();
		
		$email_template->setComplaint( $this->complaint );
		
		$email = $email_template->createEmail( $this->complaint->getEshop() );
		
		return $email->send();
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
		OrderDispatch::newByComplaint(
			complaint: $this->complaint,
			product_id: $this->complaint->getProductId(),
			delivery_method: $this->complaint->getDeliveryMethod()->getId(),
			delivery_point_code: $this->complaint->getDeliveryPersonalTakeoverDeliveryPointCode()
		);
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'New goods dispatched';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}