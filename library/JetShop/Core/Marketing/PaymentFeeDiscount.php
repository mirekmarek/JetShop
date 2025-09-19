<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use JetApplication\Discounts;
use JetApplication\EShop;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Application_Service_Admin_Marketing_PaymentFeeDiscounts;
use JetApplication\EShopEntity_Marketing;
use Jet\DataModel;
use JetApplication\EShopEntity_Definition;
use JetApplication\Application_Service_EShop_DiscountModule_PaymentFee;
use JetApplication\Payment_Method;


#[DataModel_Definition(
	name: 'payment_fee_discount',
	database_table_name: 'payment_fee_discounts',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Payment fee discount',
	admin_manager_interface: Application_Service_Admin_Marketing_PaymentFeeDiscounts::class
)]
abstract class Core_Marketing_PaymentFeeDiscount extends EShopEntity_Marketing implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Payment method:',
		is_required: true,
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value',
			Form_Field_Select::ERROR_CODE_EMPTY => 'Invalid value'
		],
		select_options_creator: [
			Payment_Method::class,
			'getScope'
		]
	)]
	protected int $payment_method_id = 0;
	
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
	
	public function getPaymentMethodId(): int
	{
		return $this->payment_method_id;
	}
	
	public function setPaymentMethodId( int $payment_method_id ): void
	{
		$this->payment_method_id = $payment_method_id;
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
	
	public static function getModule( ?EShop $eshop=null ) :null|Application_Module|Application_Service_EShop_DiscountModule_PaymentFee
	{
		return Discounts::Manager()->getActiveModuleByInterface( Application_Service_EShop_DiscountModule_PaymentFee::class, $eshop );
	}
	
	
}