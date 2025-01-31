<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\Zasilkovna;



use Jet\Config_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;
use JetApplication\EShop;

#[Config_Definition(
	name: 'Zasilkovna'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API key: ',
	)]
	protected string $API_key = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API password: ',
	)]
	protected string $API_password = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Sender ID: ',
	)]
	protected string $sender_id = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'e-shop ID: ',
	)]
	protected string $eshop_id = '';
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Allow public tracking',
	)]
	protected bool $allow_public_tracking = true;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Tracking API keys: ',
	)]
	protected string $tracking_API_keys = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'SOAP API URL: ',
	)]
	protected string $SOAP_API_URL = 'https://www.zasilkovna.cz/api/soap.wsdl';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Format of labels: ',
	)]
	protected string $labels_format = 'A7 on A4';

	
	
	public function getAPIKey(): string
	{
		return $this->API_key;
	}
	
	public function setAPIKey( string $API_key ): void
	{
		$this->API_key = $API_key;
	}
	
	public function getForm( Main $carrier, EShop $eshop ) : Form
	{
		$form = $this->createForm('cfg_form');
		
		return $form;
	}
	
	public function getAPIPassword(): string
	{
		return $this->API_password;
	}
	

	public function setAPIPassword( string $API_password ): void
	{
		$this->API_password = $API_password;
	}
	
	public function getSenderId(): string
	{
		return $this->sender_id;
	}
	
	public function setSenderId( string $sender_id ): void
	{
		$this->sender_id = $sender_id;
	}
	
	public function getEshopId(): string
	{
		return $this->eshop_id;
	}
	
	public function setEshopId( string $eshop_id ): void
	{
		$this->eshop_id = $eshop_id;
	}
	

	public function getAllowPublicTracking(): bool
	{
		return $this->allow_public_tracking;
	}
	
	public function setAllowPublicTracking( bool $allow_public_tracking ): void
	{
		$this->allow_public_tracking = $allow_public_tracking;
	}

	public function getTrackingAPIKeys(): string
	{
		return $this->tracking_API_keys;
	}
	
	public function setTrackingAPIKeys( string $tracking_API_keys ): void
	{
		$this->tracking_API_keys = $tracking_API_keys;
	}
	
	public function getSoapApiURL(): string
	{
		return $this->SOAP_API_URL;
	}

	public function setSoapApiURL( string $SOAP_API_URL ): void
	{
		$this->SOAP_API_URL = $SOAP_API_URL;
	}
	
	public function getLabelsFormat(): string
	{
		return $this->labels_format;
	}
	
	public function setLabelsFormat( string $labels_format ): void
	{
		$this->labels_format = $labels_format;
	}

	
	
}