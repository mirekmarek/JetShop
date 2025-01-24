<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Admin_Managers_MarketingDeliveryFeeDiscounts;
use JetApplication\Delivery_Method;
use JetApplication\Entity_Marketing;
use Jet\DataModel;
use JetApplication\JetShopEntity_Definition;


#[DataModel_Definition(
	name: 'delivery_fee_discount',
	database_table_name: 'delivery_fee_discounts',
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_MarketingDeliveryFeeDiscounts::class
)]
abstract class Core_Marketing_DeliveryFeeDiscount extends Entity_Marketing implements Admin_Entity_Marketing_Interface
{
	use Admin_Entity_Marketing_Trait;
	
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