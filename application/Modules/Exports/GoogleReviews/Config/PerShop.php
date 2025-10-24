<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\GoogleReviews;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShop;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;
use JetApplication\EShops;

#[Config_Definition(
	name: 'GoogleShoppingExport'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_ARRAY
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		select_options_creator: [
			EShops::class,
			'getScope'
		],
		label: 'Source: ',
	)]
	protected array $source_eshops = [];
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Agregator - name: ',
	)]
	protected string $agregator_name = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Published - name: ',
	)]
	protected string $published_name = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Published - favicon URL: ',
	)]
	protected string $published_favicon = '';
	
	
	/**
	 * @param bool $get_instances
	 * @return array<string|EShop>
	 */
	public function getSourceEShops( bool $get_instances=false ): array
	{
		if($get_instances) {
			$eshops = [];
			
			foreach( $this->source_eshops as $eshop_code ) {
				$eshops[$eshop_code] = EShops::get( $eshop_code );
			}
			
			return $eshops;
		}
		
		return $this->source_eshops;
	}

	public function setSourceEShops( array $source_eshops ): void
	{
		$this->source_eshops = $source_eshops;
	}
	
	public function getAgregatorName(): string
	{
		return $this->agregator_name;
	}
	
	public function setAgregatorName( string $agregator_name ): void
	{
		$this->agregator_name = $agregator_name;
	}
	
	public function getPublishedName(): string
	{
		return $this->published_name;
	}
	
	public function setPublishedName( string $published_name ): void
	{
		$this->published_name = $published_name;
	}
	
	public function getPublishedFavicon(): string
	{
		return $this->published_favicon;
	}
	
	public function setPublishedFavicon( string $published_favicon ): void
	{
		$this->published_favicon = $published_favicon;
	}

	
	
}