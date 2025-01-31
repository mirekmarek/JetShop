<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\Stores;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Carrier;
use JetApplication\Carrier_AdditionalConsignmentParameter;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Carrier_Document;
use JetApplication\OrderDispatch;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShop;
use JetApplication\SysServices_Provider_Interface;

class Main extends Carrier implements
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface,
	SysServices_Provider_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public const CODE = 'Stores';
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_DELIVERY;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Stores - Internal Delivery Points';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'house';
	}
	
	public function getControlCentrePriority(): int
	{
		return 10;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
	public function getConfig( EShop $eshop ) : Config_PerShop|EShopConfig_ModuleConfig_PerShop
	{
		return $this->getEshopConfig( $eshop );
	}
	
	
	public function getCarrierServiceOptions() : array
	{
		return [];
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 */
	public function downloadUpToDateDeliveryPointsList(): array
	{
		return [];
	}
	
	public function getDeliveryPointTypeOptions(): array
	{
		$points = Carrier_DeliveryPoint::getPointList( $this );
		$types = [];
		foreach($points as $p) {
			$type = $p->getPointType();
			if($type) {
				$types[$type] = $type;
			}
		}
		
		return $types;
	}
	
	public function createConsignment( OrderDispatch $dispatch ): bool
	{
		//TODO:
		return false;
	}
	
	public function cancelConsignment( OrderDispatch $dispatch, string &$error_message='' ): bool
	{
		//TODO:
		return false;
	}
	
	public function getPacketLabel( OrderDispatch $dispatch, string &$error_message='' ): ?Carrier_Document
	{
		//TODO:
		return null;
	}
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	public function getPacketLabels( array $dispatches, string &$error_message='' ): ?Carrier_Document
	{
		//TODO:
		return null;
	}
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	public function getDeliveryNote( array $dispatches, string &$error_message='' ): ?Carrier_Document
	{
		//TODO:
		return null;
	}
	
	public function getTrackingURL( OrderDispatch $dispatch ): string
	{
		return '';
	}
	
	public function actualizeTracking( OrderDispatch $dispatch, string &$error_message='' ): bool
	{
		return true;
	}
	
	
	/**
	 * @return Carrier_AdditionalConsignmentParameter[]
	 */
	public function getAdditionalConsignmentParameters() : array
	{
		return [];
	}
	
	public function getSysServicesDefinitions(): array
	{
		return [
		];
	}
}