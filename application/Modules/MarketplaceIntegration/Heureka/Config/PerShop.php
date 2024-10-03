<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\MarketplaceIntegration\Heureka;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'Heureka'
)]
class Config_PerShop extends ShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API URL: ',
		is_required: true,
	)]
	protected string $API_URL = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Client ID: ',
		is_required: true,
	)]
	protected string $API_key = '';
	
	/**
	 * @var Config_DeliveryMapItem[]
	 */
	#[Config_Definition(
		type: Config::TYPE_SECTIONS,
		section_creator_method_name: 'createDeliveryMapItem'
	)]
	protected array $delivery_map = [];
	
	/**
	 * @var Config_PaymentMapItem[]
	 */
	#[Config_Definition(
		type: Config::TYPE_SECTIONS,
		section_creator_method_name: 'createPaymentMapItem'
	)]
	protected array $payment_map = [];
	

	public function getApiUrl(): string
	{
		return $this->API_URL;
	}
	
	public function setApiUrl( string $API_URL ): void
	{
		$this->API_URL = $API_URL;
	}

	public function getAPIKey(): string
	{
		return $this->API_key;
	}

	public function setAPIKey( string $API_key ): void
	{
		$this->API_key = $API_key;
	}
	
	public function createDeliveryMapItem( array $data ): Config_DeliveryMapItem
	{
		return new Config_DeliveryMapItem( $data );
	}
	
	public function setDeliveryMapItem( Config_DeliveryMapItem $delivery_map_item ) : void
	{
		$this->delivery_map[$delivery_map_item->getDeliveryMethodId()] = $delivery_map_item;
	}
	
	public function unsetDeliveryMapItem( int $delivery_method_id ) : void
	{
		foreach($this->delivery_map as $i=>$dm) {
			if($dm->getDeliveryMethodId()==$delivery_method_id) {
				unset($this->delivery_map[$i]);
				return;
			}
		}
	}
	
	/**
	 * @return Config_DeliveryMapItem[]
	 */
	public function getDeliveryMap() : array
	{
		return $this->delivery_map;
	}
	
	public function getDeliveryMapItem( int $method_id ) : ?Config_DeliveryMapItem
	{
		return $this->delivery_map[$method_id]??null;
	}
	
	public function createPaymentMapItem( array $data ) : Config_PaymentMapItem
	{
		return new Config_PaymentMapItem( $data );
	}
	
	public function setPaymentMapItem( Config_PaymentMapItem $payment_map_item  ) : void
	{
		$this->payment_map[$payment_map_item->getPaymentMethodId()] = $payment_map_item;
	}
	
	public function unsetPaymentMapItem( int $payment_method_id ) : void
	{
		foreach($this->payment_map as $i=>$dm) {
			if($dm->getPaymentMethodId()==$payment_method_id) {
				unset($this->payment_map[$i]);
				return;
			}
		}
	}
	
	/**
	 * @return Config_PaymentMapItem[]
	 */
	public function getPaymentMap(): array
	{
		return $this->payment_map;
	}
	
	public function getPaymentMapItem( int $method_id ): ?Config_PaymentMapItem
	{
		return $this->payment_map[$method_id]??null;
	}
	
}