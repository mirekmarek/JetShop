<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\Heureka;



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
		is_required: false,
	)]
	protected string $categories_URL = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Heureka parameters CSV URL: ',
		is_required: false,
	)]
	protected string $parameters_csv_URL = '';
	
	protected function initNewConfigFile() : void
	{
		switch($this->eshop->getLocale()->toString()) {
			case 'cs_CZ':
				$this->categories_URL = 'https://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml';
				$this->parameters_csv_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vROYv0vyQXMg7c7Xu5fRTCr1fXlhWaGqRsCtST7-2jy0zQBDcSkvkqO1qawTywbQe8Xd2rPtFiMSjQR/pub?gid=0&single=true&output=csv';
				break;
			case 'sk_SK':
				$this->categories_URL = 'https://www.heureka.sk/direct/xml-export/shops/heureka-sekce.xml';
				$this->parameters_csv_URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vROYv0vyQXMg7c7Xu5fRTCr1fXlhWaGqRsCtST7-2jy0zQBDcSkvkqO1qawTywbQe8Xd2rPtFiMSjQR/pub?gid=1459300428&single=true&output=csv';
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

	public function getParametersCsvURL(): string
	{
		return $this->parameters_csv_URL;
	}
	
	public function setParametersCsvURL( string $parameters_csv_URL ): void
	{
		$this->parameters_csv_URL = $parameters_csv_URL;
	}

}