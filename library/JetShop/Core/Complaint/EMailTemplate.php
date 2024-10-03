<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Complaint;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\Shops_Shop;

abstract class Core_Complaint_EMailTemplate extends EMail_Template {
	
	protected Complaint $complaint;
	
	public function getComplaint(): Complaint
	{
		return $this->complaint;
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
		$this->complaint = $complaint;
	}
	
	public function initTest( Shops_Shop $shop ): void
	{
		$ids = Complaint::dataFetchCol(
			select: ['id'],
			where: $shop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->complaint = Complaint::get($id);
	}
	
	public function setupEMail( Shops_Shop $shop, EMail $email ): void
	{
		$email->setContext('complaint');
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
		
	}
	
}