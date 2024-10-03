<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use JetApplication\Entity_Marketing;
use Jet\DataModel;
use JetApplication\Marketing_PromoAreaDefinition;


#[DataModel_Definition(
	name: 'promo_area',
	database_table_name: 'promo_areas',
)]
abstract class Core_Marketing_PromoArea extends Entity_Marketing
{
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
	
	
}