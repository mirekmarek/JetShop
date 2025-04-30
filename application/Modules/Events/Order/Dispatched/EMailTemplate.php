<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\Dispatched;


use Jet\Tr;
use JetApplication\EShop;
use JetApplication\Order_EMailTemplate;

class EMailTemplate extends Order_EMailTemplate {
	
	protected string $carrier_name = '';
	protected string $carrier_info = '';
	protected string $consignment_number = '';
	protected string $tracking_url = '';
	
	public function getCarrierName(): string
	{
		return $this->carrier_name;
	}
	
	public function setCarrierName( string $carrier_name ): void
	{
		$this->carrier_name = $carrier_name;
	}
	
	public function getCarrierInfo(): string
	{
		return $this->carrier_info;
	}
	
	public function setCarrierInfo( string $carrier_info ): void
	{
		$this->carrier_info = $carrier_info;
	}
	
	public function getConsignmentNumber(): string
	{
		return $this->consignment_number;
	}
	
	public function setConsignmentNumber( string $consignment_number ): void
	{
		$this->consignment_number = $consignment_number;
	}
	
	public function getTrackingUrl(): string
	{
		return $this->tracking_url;
	}
	
	public function setTrackingUrl( string $tracking_url ): void
	{
		$this->tracking_url = $tracking_url;
	}
	
	
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Order - dispatched'));
		$this->setInternalNotes('');
		
		$this->addProperty('carrier_name', Tr::_('Carrier - name'))
			->setPropertyValueCreator( function() : string {
				return $this->carrier_name;
			} );
		
		$this->addProperty('carrier_info', Tr::_('Carrier - info'))
			->setPropertyValueCreator( function() : string {
				return $this->carrier_info;
			} );
		
		$this->addProperty('consignment_number', Tr::_('Consignment number'))
			->setPropertyValueCreator( function() : string {
				return $this->consignment_number;
			} );
		
		$this->addProperty('tracking_url', Tr::_('Tracking URL'))
			->setPropertyValueCreator( function() : string {
				return $this->tracking_url;
			} );
		
		$this->initCommonProperties();
	}
	
	public function initTest( EShop $eshop ): void
	{
		parent::initTest( $eshop );
		
		
		$this->carrier_name = 'Carrier ltd.';
		$this->carrier_info = 'Lorem ipsum dolor sit amet';
		$this->consignment_number = '987654321';
		$this->tracking_url = 'https://carrier/tracking/987654321';
	}
	
	
}