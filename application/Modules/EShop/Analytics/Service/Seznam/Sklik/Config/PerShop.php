<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\Seznam\Sklik;



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
		label: 'Sklik ID: ',
	)]
	protected string $id = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Zboží ID: ',
	)]
	protected string $zbozi_id = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Retargeting ID: ',
	)]
	protected string $retargeting_id = '';

	public function getId(): string
	{
		return $this->id;
	}
	
	public function setId( string $id ): void
	{
		$this->id = $id;
	}
	
	public function getZboziId(): string
	{
		return $this->zbozi_id;
	}
	
	public function setZboziId( string $zbozi_id ): void
	{
		$this->zbozi_id = $zbozi_id;
	}
	
	public function getRetargetingId(): string
	{
		return $this->retargeting_id;
	}
	
	public function setRetargetingId( string $retargeting_id ): void
	{
		$this->retargeting_id = $retargeting_id;
	}
	
	
	
}