<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'CashDesk'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Phone number validation regular expression: ',
		is_required: true,
	)]
	protected string $phone_validation_reg_exp = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Phone number prefix: ',
		is_required: true,
	)]
	protected string $phone_prefix = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Google Maps API key: ',
		is_required: true,
	)]
	protected string $map_API_key = '';
	
	#[Config_Definition(
		type: Config::TYPE_FLOAT,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Map center - lat: ',
		is_required: true,
	)]
	protected float $map_center_lat = 0.0;
	
	#[Config_Definition(
		type: Config::TYPE_FLOAT,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Map center - lon: ',
		is_required: true,
	)]
	protected float $map_center_lon = 0.0;
	
	#[Config_Definition(
		type: Config::TYPE_INT,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Map default zoom: ',
		is_required: true,
	)]
	protected int $map_default_zoom = 0;
	
	
	public function getPhoneValidationRegExp(): string
	{
		return $this->phone_validation_reg_exp;
	}
	
	public function getPhonePrefix(): string
	{
		return $this->phone_prefix;
	}
	
	public function getMapAPIKey(): string
	{
		return $this->map_API_key;
	}
	
	
	public function getMapCenterLat(): float
	{
		return $this->map_center_lat;
	}
	
	public function getMapCenterLon(): float
	{
		return $this->map_center_lon;
	}
	
	public function getMapDefaultZoom(): int
	{
		return $this->map_default_zoom;
	}
	
	public function setPhoneValidationRegExp( string $phone_validation_reg_exp ): void
	{
		$this->phone_validation_reg_exp = $phone_validation_reg_exp;
	}
	
	public function setPhonePrefix( string $phone_prefix ): void
	{
		$this->phone_prefix = $phone_prefix;
	}
	
	public function setMapAPIKey( string $map_API_key ): void
	{
		$this->map_API_key = $map_API_key;
	}
	
	public function setMapCenterLat( float $map_center_lat ): void
	{
		$this->map_center_lat = $map_center_lat;
	}
	
	public function setMapCenterLon( float $map_center_lon ): void
	{
		$this->map_center_lon = $map_center_lon;
	}
	
	public function setMapDefaultZoom( int $map_default_zoom ): void
	{
		$this->map_default_zoom = $map_default_zoom;
	}
	
}