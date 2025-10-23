<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\EShop;
use JetApplication\Product;

abstract class Core_Complaint_EMailTemplate extends EMail_Template {
	
	protected Complaint $complaint;
	protected Complaint_Event $event;
	protected string $note_for_customer = '';
	
	public function getComplaint(): Complaint
	{
		return $this->complaint;
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
		$this->complaint = $complaint;
	}
	
	public function getEvent(): Complaint_Event
	{
		return $this->event;
	}
	
	public function setEvent( Complaint_Event $event ): void
	{
		$this->event = $event;
		$this->complaint = $event->getComplaint();
		$this->note_for_customer = $event->getNoteForCustomer();
	}
	
	
	
	public function initTest( EShop $eshop ): void
	{
		$ids = Complaint::dataFetchCol(
			select: ['id'],
			where: $eshop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->complaint = Complaint::get($id);
	}
	
	public function setupEMail( EShop $eshop, EMail $email ): void
	{
		$email->setContext( Complaint::getEntityType() );
		$email->setContextId( $this->complaint->getId() );
		$email->setContextCustomerId( $this->complaint->getCustomerId() );
		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->complaint->getEmail() );
	}
	
	protected function initCommonProperties() : void
	{
		$code_property = $this->addProperty('complaint_number', Tr::_('Complaint number'));
		$code_property->setPropertyValueCreator( function() : string {
			return $this->complaint->getNumber();
		} );
		
		$purchased_date_time_property = $this->addProperty('date_time', Tr::_('Date and time of complaint'));
		$purchased_date_time_property->setPropertyValueCreator( function() : string {
			return $this->complaint->getLocale()->formatDateAndTime( $this->complaint->getDateStarted() );
		} );
		
		$URL_property = $this->addProperty('URL', Tr::_('Complaint URL'));
		$URL_property->setPropertyValueCreator( function() : string {
			return $this->complaint->getURL();
		} );
		
		
		$this->addProperty('product_name', Tr::_('Product name'))
			->setPropertyValueCreator( function() {
				$p = Product::get( $this->complaint->getProductId() );
				return $this->complaint->getProduct()->getName().' / '.$p->getInternalCode();
			});
		
		$this->addProperty('note_for_customer', Tr::_('Note for customer'))
			->setPropertyValueCreator( function() {
				if(!$this->note_for_customer) {
					return '';
				}
				
				return nl2br($this->note_for_customer);
			});
		
		$this->addCondition('has_note_for_customer', Tr::_('Has note for customer'))
			->setConditionEvaluator( function() {
				return (bool)$this->note_for_customer;
			});
		
	}
	
}