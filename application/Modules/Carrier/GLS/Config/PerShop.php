<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\GLS;



use Jet\Config_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;
use JetApplication\EShop;

#[Config_Definition(
	name: 'GLS'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Places list API URL: ',
	)]
	protected string $places_list_API_URL = '';
	
	
	public function getPlaceslistAPIURL(): string
	{
		return $this->places_list_API_URL;
	}
	
	public function setPlaceslistAPIURL( string $places_list_API_URL ): void
	{
		$this->places_list_API_URL = $places_list_API_URL;
	}
	
	public function getForm( Main $carrier, EShop $eshop ) : Form
	{
		$form = $this->createForm('cfg_form');
		
		return $form;
	}
}