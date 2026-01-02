<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\DogNet;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'DogNet'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Account ID: ',
	)]
	protected string $account_id = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Campaign ID: ',
	)]
	protected string $campaign_id = '';

	public function getAccountId(): string
	{
		return $this->account_id;
	}
	
	public function setAccountId( string $account_id ): void
	{
		$this->account_id = $account_id;
	}
	
	public function getCampaignId(): string
	{
		return $this->campaign_id;
	}
	
	public function setCampaignId( string $campaign_id ): void
	{
		$this->campaign_id = $campaign_id;
	}
	
	
}