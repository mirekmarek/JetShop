<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Payment\GP;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'GPWebPay'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API URL: ',
		is_required: true,
	)]
	protected string $API_URL = 'https://3dsecure.gpwebpay.com/pgw/order.do?';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'WSDL file name: ',
		is_required: true,
	)]
	protected string $WSDL_file_name = 'cws_v1.wsdl';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'SOAP endpoint: ',
		is_required: true,
	)]
	protected string $SOAP_endpoint = 'https://3dsecure.gpwebpay.com/pay-ws/v1/PaymentService';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Payment provider: ',
		is_required: true,
	)]
	protected string $payment_provider = '0880';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Private key: ',
		is_required: true,
	)]
	protected string $private_key = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Key ppassword: ',
		is_required: true,
	)]
	protected string $key_password = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Merchant number: ',
		is_required: true,
	)]
	protected string $merchant_number = '';
	
	public function getAPIURL(): string
	{
		return $this->API_URL;
	}
	
	public function setAPIURL( string $API_URL ): void
	{
		$this->API_URL = $API_URL;
	}
	
	public function getWSDLFileName(): string
	{
		return $this->WSDL_file_name;
	}
	
	public function setWSDLFileName( string $WSDL_file_name ): void
	{
		$this->WSDL_file_name = $WSDL_file_name;
	}
	
	public function getSOAPEndpoint(): string
	{
		return $this->SOAP_endpoint;
	}
	
	public function setSOAPEndpoint( string $SOAP_endpoint ): void
	{
		$this->SOAP_endpoint = $SOAP_endpoint;
	}
	
	public function getPaymentProvider(): string
	{
		return $this->payment_provider;
	}
	
	public function setPaymentProvider( string $payment_provider ): void
	{
		$this->payment_provider = $payment_provider;
	}
	
	public function getKeyPassword(): string
	{
		return $this->key_password;
	}
	
	public function setKeyPassword( string $key_password ): void
	{
		$this->key_password = $key_password;
	}
	
	public function getPrivateKey(): string
	{
		return $this->private_key;
	}
	
	public function setPrivateKey( string $private_key ): void
	{
		$this->private_key = $private_key;
	}
	
	public function getMerchantNumber(): string
	{
		return $this->merchant_number;
	}
	
	public function setMerchantNumber( string $merchant_number ): void
	{
		$this->merchant_number = $merchant_number;
	}

	
	
}