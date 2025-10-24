<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\EComail;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'EComail'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API URL: ',
		is_required: false,
	)]
	protected string $API_URL = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API key: ',
		is_required: false,
	)]
	protected string $API_key = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'List ID: ',
		is_required: false,
	)]
	protected int $list_id = 0;
	

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
	
	public function getListId(): int
	{
		return $this->list_id;
	}
	
	public function setListId( int $list_id ): void
	{
		$this->list_id = $list_id;
	}
	
	
	
}