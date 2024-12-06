<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Carrier\CeskaPosta;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_General;
use Jet\Config;

#[Config_Definition(
	name: 'CeskaPosta'
)]
class Config_General extends EShopConfig_ModuleConfig_General implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'XML - Pošty: ',
		is_required: true,
	)]
	protected string $XML_URL_posta = 'http://napostu.ceskaposta.cz/vystupy/napostu.xml';
	
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'XML - Balíkovny: ',
		is_required: true,
	)]
	protected string $XML_URL_balikovna = 'http://napostu.ceskaposta.cz/vystupy/balikovny.xml';
	
	public function getXMLURLPosta(): string
	{
		return $this->XML_URL_posta;
	}
	
	public function setXMLURLPosta( string $XML_URL_posta ): void
	{
		$this->XML_URL_posta = $XML_URL_posta;
	}
	
	public function getXMLURLBalikovna(): string
	{
		return $this->XML_URL_balikovna;
	}

	public function setXMLURLBalikovna( string $XML_URL_balikovna ): void
	{
		$this->XML_URL_balikovna = $XML_URL_balikovna;
	}
	
	
}