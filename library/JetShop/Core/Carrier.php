<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;

use JetApplication\Carrier;
use JetApplication\Carrier_Document;
use JetApplication\Delivery_Method_EShopData;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Carrier_Service;
use JetApplication\Managers;
use JetApplication\OrderDispatch;
use JetApplication\Carrier_AdditionalConsignmentParameter;


abstract class Core_Carrier extends Application_Module
{
	public const CODE = null;
	
	public const DP_TYPE_BRANCH = 'branch';
	public const DP_TYPE_BOX = 'box';
	
	
	public function getCode() : string
	{
		return static::CODE;
	}
	
	public function getName() : string
	{
		return $this->module_manifest->getLabel();
	}
	
	/**
	 * @return Carrier_Service[]
	 */
	public function getServices() : array
	{
		/**
		 * @var Carrier $this
		 */
		return Carrier_Service::getForCarrier( $this );
	}
	
	public function getService( string $code ) : ?Carrier_Service
	{
		return $this->getServices()[$code]??null;
	}
	
	public function getServicesList() : array
	{
		$res = [];
		
		foreach( $this->getServices() as $service ) {
			$res[$service->getCode()] = $service->getName();
		}
		
		return $res;
	}
	
	
	/**
	 * @param Delivery_Method_EShopData $delivery_method
	 * @return Carrier_Service[]
	 */
	public function getPossibleServices( Delivery_Method_EShopData $delivery_method ) : array
	{
		$all_services = $this->getServices();
		$possible_services = [];
		
		$method_locale = $delivery_method->getLocale();
		$method_kind = $delivery_method->getKindCode();
		
		foreach($all_services as $service) {
			if(
				$service->getCompatibleKindOfDelivery()==$method_kind
			) {
				$possible_services[$service->getCode()] = $service;
			}
		}
		
		return $possible_services;
	}
	
	
	public function getPossibleServicesScope( Delivery_Method_EShopData $delivery_method ) : array
	{
		$scope = [''=>''];
		foreach($this->getPossibleServices( $delivery_method ) as $service) {
			$scope[$service->getCode()] = $service->getName();
		}
		
		return $scope;
	}
	
	/**
	 *
	 * @return Carrier_DeliveryPoint[]
	 */
	abstract public function downloadUpToDateDeliveryPointsList() : array;
	
	
	public function actualizeDeliveryPoints( bool $verbose=true ) : void
	{
		if($verbose) {
			$log = function( string $message ) {
				echo $message;
			};
		} else {
			$log = function( string $message ) {};
		}
		
		$log($this->getName()."\n");
		$log("\tgetting current list from carrier's API ...\n");
		
		
		$future_list = [];
		foreach( $this->downloadUpToDateDeliveryPointsList() as $place) {
			$future_list[$place->generateHash()] = $place;
		}
		
		$log("\t\tDONE\n");
		
		
		if(!$future_list) {
			$log("\tlist is empty - skipping\n");
			return;
		}
		
		
		$log("\t\treading current map ...\n");
		
		$current_map = Carrier_DeliveryPoint::getHashMap( $this );
		
		$log( "\t\tDONE\n" );
		
		$log("\t\tdeleting ...\n");
		foreach($current_map as $hash=>$id) {
			if(!isset($future_list[$hash])) {
				$place = Carrier_DeliveryPoint::load( (int)$id );
				$place?->delete();
			}
		}
		$log( "\t\tDONE\n" );
		
		$log("\t\tadding ...\n");
		foreach($future_list as $hash=>$place) {
			if(!isset($current_map[$hash])) {
				$log( "\t\t {$place->getKey()} - adding\n" );
				$place->save();
			}
		}
		$log( "\t\tDONE\n" );
		
		
	}
	
	/**
	 * @return Carrier[]
	 */
	public static function getList() : array
	{
		$modules = [];
		
		foreach( Managers::findManagers(Carrier::class, 'Carrier.') as $module) {
			/**
			 * @var Carrier $module
			 */
			$modules[ $module->getCode() ] = $module;
		}
		
		return $modules;
	}
	
	public static function get( string $code ) : ?static
	{
		return static::getList()[$code]??null;
	}
	
	public static function getScope() : array
	{
		$scope = [''=>''];
		
		foreach(static::getList() as $carrier) {
			$scope[$carrier->getCode()] = $carrier->getName();
		}
		
		return $scope;
	}
	
	abstract public function getCarrierServiceOptions() : array;
	
	abstract public function getDeliveryPointTypeOptions() : array;

	abstract public function createConsignment( OrderDispatch $dispatch ) : bool;
	
	abstract public function cancelConsignment( OrderDispatch $dispatch, string &$error_message='' ): bool;
	
	abstract public function getPacketLabel( OrderDispatch $dispatch, string &$error_message='' ): ?Carrier_Document;
	
	abstract public function actualizeTracking( OrderDispatch $dispatch, string &$error_message='' ): bool;
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	abstract public function getPacketLabels( array $dispatches, string &$error_message='' ): ?Carrier_Document;
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	abstract public function getDeliveryNote( array $dispatches, string &$error_message='' ): ?Carrier_Document;
	
	
	abstract public function getTrackingURL( OrderDispatch $dispatch ): string;
	
	/**
	 * @return Carrier_AdditionalConsignmentParameter[]
	 */
	abstract public function getAdditionalConsignmentParameters() : array;
}