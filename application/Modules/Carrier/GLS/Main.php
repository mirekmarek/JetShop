<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\GLS;


use Jet\Exception;
use Jet\Tr;
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
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;

class Main extends Carrier implements
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface,
	SysServices_Provider_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public const CODE = 'GLS';
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_DELIVERY;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'GLS';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'truck-ramp-box';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
	public function getConfig( EShop $eshop ) : Config_PerShop|EShopConfig_ModuleConfig_PerShop
	{
		return $this->getEshopConfig( $eshop );
	}
	
	public function getClient() : Client
	{
		return new Client( $this );
	}
	
	public function getCarrierServiceOptions() : array
	{
		return [];
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 * @throws Exception
	 */
	public function downloadUpToDateDeliveryPointsList(): array
	{
		return $this->getClient()->downloadUpToDateDeliveryPointsList();
	}
	
	public function getDeliveryPointTypeOptions(): array
	{
		return [
		];
	}
	
	public function createConsignment( OrderDispatch $dispatch ): bool
	{
		return false;
	}
	
	public function cancelConsignment( OrderDispatch $dispatch, string &$error_message='' ): bool
	{
		return false;
	}
	
	public function getPacketLabel( OrderDispatch $dispatch, string &$error_message='' ): ?Carrier_Document
	{
		return null;
	}
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	public function getPacketLabels( array $dispatches, string &$error_message='' ): ?Carrier_Document
	{
		return null;
	}
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	public function getDeliveryNote( array $dispatches, string &$error_message='' ): ?Carrier_Document
	{
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
		$actualize_points_service = new SysServices_Definition(
			module:        $this,
			name:          Tr::_( 'GLS - Actualize delivery points' ),
			description:   Tr::_( 'Updates the list of points where the consignment can be delivered / where the customer can pick up the consignment.' ),
			service_code: 'actualize_delivery_points',
			service:       function() {
				$this->actualizeDeliveryPoints( true );
			}
		);
		
		
		return [
			$actualize_points_service,
		];
	}
}