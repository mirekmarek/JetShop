<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Complaints;



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
	name: 'Complaints'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Send notifications to: ',
	)]
	protected string $send_notifications_to = '';
	
	public function getSendNotificationsTo(): string
	{
		return $this->send_notifications_to;
	}
	
	public function setSendNotificationsTo( string $send_notifications_to ): void
	{
		$this->send_notifications_to = $send_notifications_to;
	}
	
	

	
	public function getForm( Main $carrier, EShop $eshop ) : Form
	{
		$form = $this->createForm('cfg_form');
		
		return $form;
	}
}