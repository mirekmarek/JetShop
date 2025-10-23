<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\Seznam\Zbozi;



use Jet\Config;
use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;

#[Config_Definition(
	name: 'SeznamSklik'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Zboží - ID: ',
	)]
	protected string $id = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Zboží - klíč: ',
	)]
	protected string $key = '';
	
	public function getId(): string
	{
		return $this->id;
	}
	
	public function setId( string $id ): void
	{
		$this->id = $id;
	}
	
	
	public function getKey(): string
	{
		return $this->key;
	}
	
	public function setKey( string $key ): void
	{
		$this->key = $key;
	}
}