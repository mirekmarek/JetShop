<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Marketing;
use Jet\DataModel;
use JetApplication\Marketing_PromoAreaDefinition;
use JetApplication\Admin_Managers_Marketing_PromoAreas;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'promo_area',
	database_table_name: 'promo_areas',
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_Marketing_PromoAreas::class
)]
abstract class Core_Marketing_PromoArea extends EShopEntity_Marketing implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Promo area:',
		is_required: true,
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value',
			Form_Field_Select::ERROR_CODE_EMPTY => 'Invalid value'
		],
		select_options_creator: [
			Marketing_PromoAreaDefinition::class,
			'getScope'
		]
	)]
	protected int $promo_area_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len : 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'HTML:'
	)]
	protected string $html = '';
	

	public function getPromoAreaId(): int
	{
		return $this->promo_area_id;
	}

	public function setPromoAreaId( int $promo_area_id ): void
	{
		$this->promo_area_id = $promo_area_id;
	}
	
	public function getHtml(): string
	{
		return $this->html;
	}
	
	public function setHtml( string $html ): void
	{
		$this->html = $html;
	}
	
	public function hasImages(): bool
	{
		return false;
	}
	
}