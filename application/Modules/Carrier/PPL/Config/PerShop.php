<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Carrier\PPL;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'Zasilkovna'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'URL - JSON - pobočky: ',
		is_required: true,
	)]
	protected string $URL_JSON_branches = '';
	

	public function getURLJSONBranches(): string
	{
		return $this->URL_JSON_branches;
	}
	
	public function setURLJSONBranches( string $URL_JSON_branches ): void
	{
		$this->URL_JSON_branches = $URL_JSON_branches;
	}
	
	
	
}