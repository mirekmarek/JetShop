<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Locale;

trait Core_CommonEntity_ShopRelationTrait_ShopIsId {
	use CommonEntity_ShopRelationTrait;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		is_id: true,
	)]
	protected string $shop_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
		is_id: true,
	)]
	protected ?Locale $locale = null;

}