<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\Analytics\Service\Seznam;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'Seznam'
)]
class Config_PerShop extends ShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Seznam ID: ',
		is_required: true,
	)]
	protected string $seznam_id = '';

	public function getSeznamId(): string
	{
		return $this->seznam_id;
	}
	
	public function setSeznamId( string $seznam_id ): void
	{
		$this->seznam_id = $seznam_id;
	}
}