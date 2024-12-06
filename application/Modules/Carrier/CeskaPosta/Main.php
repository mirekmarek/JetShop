<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Carrier\CeskaPosta;

use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Carrier;
use JetApplication\Carrier_AdditionalConsignmentParameter;
use JetApplication\Carrier_Document;
use JetApplication\OrderDispatch;
use JetApplication\EShopConfig_ModuleConfig_General;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;

/**
 *
 */
class Main extends Carrier implements EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public const CODE = 'CeskaPosta';
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_DELIVERY;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Česká pošta';
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
		return false;
	}
	
	public function getConfig() : Config_General|EShopConfig_ModuleConfig_General
	{
		return $this->getGeneralConfig();
	}
	
	public function getClient() : Client
	{
		return new Client( $this );
	}
	
	public function downloadUpToDateDeliveryPointsList(): array
	{
		return $this->getClient()->downloadUpToDateDeliveryPointsList();
	}
	
	
	public function getCarrierServiceOptions(): array
	{
		// TODO: Implement getCarrierServiceOptions() method.
		return [];
	}
	
	public function getDeliveryPointTypeOptions(): array
	{
		return [
			'posta' => Tr::_('Pošta', dictionary: $this->module_manifest->getName()),
			'balikovna' => Tr::_('Balíkovna', dictionary: $this->module_manifest->getName()),
		];
	}
	
	
	public function createConsignment( OrderDispatch $dispatch ): bool
	{
		// TODO: Implement createConsignment() method.
		return true;
	}
	
	public function cancelConsignment( OrderDispatch $dispatch, string &$error_message='' ): bool
	{
		// TODO: Implement cancelConsignment() method.
		return false;
	}
	
	
	public function getPacketLabel( OrderDispatch $dispatch, string &$error_message='' ): ?Carrier_Document
	{
		// TODO: Implement cancelConsignment() method.
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
	
	public function getTrackingURL( OrderDispatch $dispatch ): string
	{
		//TODO:
		return '';
	}
	
	public function actualizeTracking( OrderDispatch $dispatch, string &$error_message='' ): bool
	{
		//TODO:
		return false;
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
	
	
	/**
	 * @return Carrier_AdditionalConsignmentParameter[]
	 */
	public function getAdditionalConsignmentParameters() : array
	{
		return [];
	}
	
}