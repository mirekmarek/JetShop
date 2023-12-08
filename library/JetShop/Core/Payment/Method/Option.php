<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel;
use Jet\DataModel_Related_1toN;

use JetApplication\Payment_Method_Option_ShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'payment_methods_options',
	database_table_name: 'payment_methods_options',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Core_Payment_Method::class
)]
abstract class Core_Payment_Method_Option extends DataModel_Related_1toN {

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_id: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Code:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
			'code_used' => 'This code is already used',
		]
	)]
	protected string $code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
		related_to: 'main.code'
	)]
	protected string $payment_method_code = '';

	/**
	 * @var Payment_Method_Option_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_Option_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];



	public function isInherited() : bool
	{
		return true;
	}

	public function getPaymentMethodCode() : int
	{
		return $this->payment_method_code;
	}


	public function getCode() : string
	{
		return $this->code;
	}

	public function setCode( string $code ) : void
	{
		$this->code = $code;
	}

	public function getArrayKeyValue() : string
	{
		return $this->code;
	}

	public function getShopData( ?Shops_Shop $shop=null ) : Payment_Method_Option_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

	public function getDescription( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData( $shop )->getDescription();
	}

	public function getTitle( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData( $shop )->getTitle();
	}


}