<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\ReturnOfGoods;
use JetApplication\Shops_Shop;

abstract class Core_ReturnOfGoods_EMailTemplate extends EMail_Template {
	
	protected ReturnOfGoods $return_of_goods;
	
	public function getReturnOfGoods(): ReturnOfGoods
	{
		return $this->return_of_goods;
	}
	
	public function setReturnOfGoods( ReturnOfGoods $return_of_goods ): void
	{
		$this->return_of_goods = $return_of_goods;
	}
	
	public function initTest( Shops_Shop $shop ): void
	{
		$ids = ReturnOfGoods::dataFetchCol(
			select: ['id'],
			where: $shop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		
		$this->return_of_goods = ReturnOfGoods::get( $id );
	}
	
	public function setupEMail( Shops_Shop $shop, EMail $email ): void
	{
		$email->setContext('return_of_goods');
		$email->setContextId( $this->return_of_goods->getId() );
		$email->setContextCustomerId( $this->return_of_goods->getCustomerId() );
		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->return_of_goods->getEmail() );
	}
	
	protected function initCommonProperties() : void
	{
		$code_property = $this->addProperty('rog_number', Tr::_('Return of goods number'));
		$code_property->setPropertyValueCreator( function() : string {
			return $this->return_of_goods->getNumber();
		} );
		
		$purchased_date_time_property = $this->addProperty('date_time', Tr::_('Date and time of Return of goods'));
		$purchased_date_time_property->setPropertyValueCreator( function() : string {
			return $this->return_of_goods->getLocale()->formatDateAndTime( $this->return_of_goods->getDateStarted() );
		} );
		
		$URL_property = $this->addProperty('URL', Tr::_('Return of goods URL'));
		$URL_property->setPropertyValueCreator( function() : string {
			return $this->return_of_goods->getURL();
		} );
		
	}
	
}