<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\ZboziCZ;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'ZboziCZ'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Zboží CZ categories URL: ',
		is_required: false,
	)]
	protected string $categories_URL = '';
	
	protected function initNewConfigFile() : void
	{
		/** @noinspection PhpSwitchStatementWitSingleBranchInspection */
		switch($this->eshop->getLocale()->toString()) {
			case 'cs_CZ':
				$this->categories_URL = 'https://www.zbozi.cz/static/categories.json';
				break;
			
		}
		
		parent::initNewConfigFile();
	}
	
	public function getCategoriesURL(): string
	{
		return $this->categories_URL;
	}

	public function setCategoriesURL( string $categories_URL ): void
	{
		$this->categories_URL = $categories_URL;
	}


}