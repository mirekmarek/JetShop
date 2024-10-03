<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\Analytics\Service\Facebook;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'Facebook'
)]
class Config_PerShop extends ShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Facebook ID: ',
		is_required: true,
	)]
	protected string $facebook_id = '';

	public function getFacebookId(): string
	{
		return $this->facebook_id;
	}
	
	public function setFacebookId( string $facebook_id ): void
	{
		$this->facebook_id = $facebook_id;
	}
}