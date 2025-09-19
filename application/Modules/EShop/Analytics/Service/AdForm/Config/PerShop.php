<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\AdForm;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'Seznam'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Account ID: ',
		is_required: true,
	)]
	protected string $account_id = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Page name prefix: ',
	)]
	protected string $page_name_prefix = '';
	
	public function getAccountId(): string
	{
		return $this->account_id;
	}
	
	public function setAccountId( string $account_id ): void
	{
		$this->account_id = $account_id;
	}
	
	public function getPageNamePrefix(): string
	{
		return $this->page_name_prefix;
	}
	
	public function setPageNamePrefix( string $page_name_prefix ): void
	{
		$this->page_name_prefix = $page_name_prefix;
	}
	
	
}