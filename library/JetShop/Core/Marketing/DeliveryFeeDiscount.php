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
use JetApplication\Admin_Managers_Marketing_DeliveryFeeDiscounts;
use JetApplication\Delivery_Method;
use JetApplication\EShopEntity_Marketing;
use Jet\DataModel;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'delivery_fee_discount',
	database_table_name: 'delivery_fee_discounts',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Delivery fee discount',
	admin_manager_interface: Admin_Managers_Marketing_DeliveryFeeDiscounts::class
)]
abstract class Core_Marketing_DeliveryFeeDiscount extends EShopEntity_Marketing implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Delivery method:',
		is_required: true,
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value',
			Form_Field_Select::ERROR_CODE_EMPTY => 'Invalid value'
		],
		select_options_creator: [
			Delivery_Method::class,
			'getScope'
		]
	)]
	protected int $delivery_method_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len : 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Promo HTML:'
	)]
	protected string $promo_html = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Discount percentage:'
	)]
	protected float $discount_percentage = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Order amount limit:'
	)]
	protected float $amount_limit = 0.0;

	public function getDeliveryMethodId(): int
	{
		return $this->delivery_method_id;
	}

	public function setDeliveryMethodId( int $delivery_method_id ): void
	{
		$this->delivery_method_id = $delivery_method_id;
	}
	
	public function getPromoHtml(): string
	{
		return $this->promo_html;
	}
	
	public function setPromoHtml( string $promo_html ): void
	{
		$this->promo_html = $promo_html;
	}
	
	public function getDiscountPercentage(): float
	{
		return $this->discount_percentage;
	}

	public function setDiscountPercentage( float $discount_percentage ): void
	{
		$this->discount_percentage = $discount_percentage;
	}

	public function getAmountLimit(): float
	{
		return $this->amount_limit;
	}

	public function setAmountLimit( float $amount_limit ): void
	{
		$this->amount_limit = $amount_limit;
	}
	
	public function hasImages(): bool
	{
		return false;
	}
	
	
}