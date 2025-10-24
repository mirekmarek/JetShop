<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\Facebook;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'FacebookExport'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title: ',
		is_required: false,
	)]
	protected string $title = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description: ',
		is_required: false,
	)]
	protected string $description = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Link: ',
		is_required: false,
	)]
	protected string $link = '';
	
	public function setTitle( string $title ): void
	{
		$this->title = $title;
	}
	
	public function setDescription( string $description ): void
	{
		$this->description = $description;
	}
	
	public function setLink( string $link ): void
	{
		$this->link = $link;
	}
	
	
	
	public function getTitle(): string
	{
		return $this->title;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function getLink(): string
	{
		return $this->link;
	}

	
}