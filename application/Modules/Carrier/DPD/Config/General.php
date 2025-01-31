<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Carrier\DPD;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_General;
use Jet\Config;

#[Config_Definition(
	name: 'DPD'
)]
class Config_General extends EShopConfig_ModuleConfig_General implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has dimensions',
	)]
	protected bool $courier_packaging_has_dimensions = false;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has weight',
	)]
	protected bool $courier_packaging_has_weight = true;
	
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has dimensions',
	)]
	protected bool $delivery_point_packaging_has_dimensions = false;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has weight',
	)]
	protected bool $delivery_point_packaging_has_weight = true;
	
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has dimensions',
	)]
	protected bool $box_packaging_has_dimensions = false;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has weight',
	)]
	protected bool $box_packaging_has_weight = true;
	
	
	
	
	public function getCourierPackagingHasDimensions(): bool
	{
		return $this->courier_packaging_has_dimensions;
	}
	
	public function setCourierPackagingHasDimensions( bool $courier_packaging_has_dimensions ): void
	{
		$this->courier_packaging_has_dimensions = $courier_packaging_has_dimensions;
	}
	
	public function getCourierPackagingHasWeight(): bool
	{
		return $this->courier_packaging_has_weight;
	}
	
	public function setCourierPackagingHasWeight( bool $courier_packaging_has_weight ): void
	{
		$this->courier_packaging_has_weight = $courier_packaging_has_weight;
	}
	
	public function getDeliveryPointPackagingHasDimensions(): bool
	{
		return $this->delivery_point_packaging_has_dimensions;
	}
	
	public function setDeliveryPointPackagingHasDimensions( bool $delivery_point_packaging_has_dimensions ): void
	{
		$this->delivery_point_packaging_has_dimensions = $delivery_point_packaging_has_dimensions;
	}
	
	public function getDeliveryPointPackagingHasWeight(): bool
	{
		return $this->delivery_point_packaging_has_weight;
	}
	
	public function setDeliveryPointPackagingHasWeight( bool $delivery_point_packaging_has_weight ): void
	{
		$this->delivery_point_packaging_has_weight = $delivery_point_packaging_has_weight;
	}
	
	public function getBoxPackagingHasDimensions(): bool
	{
		return $this->box_packaging_has_dimensions;
	}
	
	public function setBoxPackagingHasDimensions( bool $box_packaging_has_dimensions ): void
	{
		$this->box_packaging_has_dimensions = $box_packaging_has_dimensions;
	}
	
	public function getBoxPackagingHasWeight(): bool
	{
		return $this->box_packaging_has_weight;
	}
	
	public function setBoxPackagingHasWeight( bool $box_packaging_has_weight ): void
	{
		$this->box_packaging_has_weight = $box_packaging_has_weight;
	}
}