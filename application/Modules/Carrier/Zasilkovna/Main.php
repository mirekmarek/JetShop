<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Carrier\Zasilkovna;

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
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use JetApplication\Shops_Shop;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;

class Main extends Carrier implements
	ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface,
	SysServices_Provider_Interface
{
	use ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public const CODE = 'Zasilkovna';
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_DELIVERY;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Zásilkovna';
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
	
	public function getConfig( Shops_Shop $shop ) : Config_PerShop|ShopConfig_ModuleConfig_PerShop
	{
		return $this->getShopConfig( $shop );
	}
	
	public function getClient() : Client
	{
		return new Client( $this );
	}
	
	public function getCarrierServiceOptions() : array
	{
		$list = $this->getClient()->getCarriers();
		$res = [];
		foreach($list as $id=>$c) {
			$res[$id] = $c['name'];
		}
		
		asort( $res );
		
		return $res;
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
			static::DP_TYPE_BRANCH => Tr::_('Branch', dictionary: $this->module_manifest->getName()),
			static::DP_TYPE_BOX => Tr::_('Box', dictionary: $this->module_manifest->getName()),
		];
	}
	
	public function createConsignment( OrderDispatch $dispatch ): bool
	{
		return $this->getClient()->createConsignment( $dispatch );
	}
	
	public function cancelConsignment( OrderDispatch $dispatch, string &$error_message='' ): bool
	{
		return $this->getClient()->cancelConsignment( $dispatch, $error_message );
	}
	
	public function getPacketLabel( OrderDispatch $dispatch, string &$error_message='' ): ?Carrier_Document
	{
		return $this->getClient()->getPacketLabel( $dispatch, $error_message );
	}
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	public function getPacketLabels( array $dispatches, string &$error_message='' ): ?Carrier_Document
	{
		
		return $this->getClient()->getPacketLabels( $dispatches, $error_message );
	}
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	public function getDeliveryNote( array $dispatches, string &$error_message='' ): ?Carrier_Document
	{
		return $this->getClient()->getDeliveryNote( $dispatches, $error_message );
	}
	
	public function getTrackingURL( OrderDispatch $dispatch ): string
	{
		$tracking_number = $dispatch->getTrackingNumber();
		if( !$tracking_number ) {
			return '';
		}
		
		return 'https://tracking.packeta.com/'.$dispatch->getShop()->getLocale().'/?id='.$dispatch->getTrackingNumber();
	}
	
	public function actualizeTracking( OrderDispatch $dispatch, string &$error_message='' ): bool
	{
		$id = $dispatch->getConsignmentId();
		if( !$id ) {
			return true;
		}
		
		return $this->getClient()->actualizeTracking( $dispatch, $error_message );
	}
	
	
	/**
	 * @return Carrier_AdditionalConsignmentParameter[]
	 */
	public function getAdditionalConsignmentParameters() : array
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() : array
			{
				return [
					new Carrier_AdditionalConsignmentParameter(
						code: 'adultContent',
						name: Tr::_('Verify age'),
						description: Tr::_('Upon delivery, it will be verified whether the addressee is 18 years old.')
					)
				];
			}
		);
	}
	
	public function getSysServicesDefinitions(): array
	{
		$actualize_points_service = new SysServices_Definition(
			module:        $this,
			name:          Tr::_( 'Zasilkovna - Actualize delivery points' ),
			description:   Tr::_( 'Updates the list of points where the consignment can be delivered / where the customer can pick up the consignment.' ),
			service_code: 'actualize_delivery_points',
			service:       function() {
				$this->actualizeDeliveryPoints( true );
			}
		);
		
		$actualize_tracking_service = new SysServices_Definition(
			module:        $this,
			name:          Tr::_( 'Zasilkovna - Actualize tracking' ),
			description:   Tr::_( 'Updates the status of delivered consignments.' ),
			service_code: 'actualize_tracking',
			service:       function() {
				//TODO:
			}
		);
		
		
		return [
			$actualize_points_service,
			$actualize_tracking_service
		];
	}
}