<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Exports\GoogleShopping;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'HeurekaExport'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Heureka categories URL: ',
		is_required: true,
	)]
	protected string $categories_URL = '';
	
	
	protected function initNewConfigFile() : void
	{
		$locale = $this->eshop->getLocale();
		
		$lng = $locale->getLanguage();
		$reg = $locale->getRegion();
		
		$this->categories_URL = 'https://www.google.com/basepages/producttype/taxonomy-with-ids.'.$lng.'-'.$reg.'.txt';
		
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