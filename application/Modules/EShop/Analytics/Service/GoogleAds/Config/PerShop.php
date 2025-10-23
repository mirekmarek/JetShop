<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\GoogleAds;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'GoogleAds'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'ID: ',
	)]
	protected string $account_id = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Send to ID: ',
	)]
	protected string $send_to_id = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Conversion Code: ',
	)]
	protected string $conversion_js_code = '';

	public function getAccountId(): string
	{
		return $this->account_id;
	}
	
	public function setAccountId( string $account_id ): void
	{
		$this->account_id = $account_id;
	}
	
	public function getSendToId(): string
	{
		return $this->send_to_id;
	}
	
	public function setSendToId( string $send_to_id ): void
	{
		$this->send_to_id = $send_to_id;
	}
	
	public function getConversionJsCode(): string
	{
		return $this->conversion_js_code;
	}
	
	public function setConversionJsCode( string $conversion_js_code ): void
	{
		$this->conversion_js_code = $conversion_js_code;
	}
}