<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;

use JetApplication\Carrier;
use JetApplication\Delivery_Method_Price;
use JetApplication\Entity_HasPrice_Interface;
use JetApplication\Entity_HasPrice_Trait;
use JetApplication\Entity_WithEShopData;
use JetApplication\Pricelist;
use JetApplication\EShop;
use JetApplication\Delivery_Kind;
use JetApplication\Delivery_Class;
use JetApplication\Delivery_Method_Class;
use JetApplication\Delivery_Method_PaymentMethods;
use JetApplication\Payment_Method;
use JetApplication\Delivery_Method_EShopData;
use JetApplication\EShops;

#[DataModel_Definition(
	name: 'delivery_method',
	database_table_name: 'delivery_methods',
)]
abstract class Core_Delivery_Method extends Entity_WithEShopData implements Entity_HasPrice_Interface
{
	use Entity_HasPrice_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		is_required: true,
		label: 'Kind:',
		select_options_creator: [
			Delivery_Kind::class,
			'getScope'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select kind',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind'
		]
	)]
	protected string $kind = '';


	/**
	 * @var Delivery_Method_Class[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_Class::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Classes:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please select delivery class',
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select delivery class'
		],
		default_value_getter_name: 'getDeliveryClassIds',
		select_options_creator: [Delivery_Class::class, 'getScope']
	)]
	protected array $delivery_classes = [];

	
	
	/**
	 * @var Delivery_Method_PaymentMethods[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_PaymentMethods::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Payment methods:',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select payment method',
			Form_Field::ERROR_CODE_EMPTY => 'Please select payment method'
		],
		default_value_getter_name: 'getPaymentMethodsIds',
		select_options_creator: [Payment_Method::class, 'getScope']
	)]
	protected array $payment_methods = [];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Carrier:',
		select_options_creator: [Carrier::class, 'getScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select carrier',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select carrier'
		]
	)]
	protected string $carrier_code = '';

	/**
	 * @var Delivery_Method_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_EShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $eshop_data = [];
	
	
	public function getPriceEntity( Pricelist $pricelist ) : Delivery_Method_Price
	{
		return Delivery_Method_Price::get( $pricelist, $this->getId() );
	}
	
	
	public function setKind( string $code ): void
	{
		$this->kind = $code;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData($eshop)->setKind($code);
		}
	}

	public function getKindCode(): string
	{
		return $this->kind;
	}

	public function getKind() : ?Delivery_Kind
	{
		return Delivery_Kind::get( $this->kind );
	}

	public function getKindTitle() : string
	{
		return $this->getKind()?->getTitle()?:'';
	}
	
	public function getCarrierCode(): string
	{
		return $this->carrier_code;
	}
	
	public function setCarrierCode( string $carrier_code ): void
	{
		$this->carrier_code = $carrier_code;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->setCarrierCode( $carrier_code );
		}
	}
	

	public function getEshopData( ?EShop $eshop=null ) : Delivery_Method_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}

	/**
	 * @param array $ids
	 */
	public function setDeliveryClasses( array $ids ) : void
	{
		foreach($this->delivery_classes as $id=>$r) {
			if(!in_array($id, $ids)) {
				$r->delete();
				unset($this->delivery_classes[$id]);
			}
		}

		foreach( $ids as $id ) {

			if(!isset($this->delivery_classes[$id])) {
				if( !Delivery_Class::exists( $id ) ) {
					continue;
				}
				
				$new_item = new Delivery_Method_Class();
				if($this->id) {
					$new_item->setMethodId( $this->id );
				}
				$new_item->setClassId( $id );

				$this->delivery_classes[$id] = $new_item;
				if($this->id) {
					$new_item->save();
				}
			}
		}
	}

	public function getDeliveryClassIds() : array
	{
		return array_keys($this->delivery_classes);
	}
	

	public function isPersonalTakeover() : bool
	{
		return $this->kind == Delivery_Kind::KIND_PERSONAL_TAKEOVER;
	}

	public function isEDelivery() : bool
	{
		return $this->kind == Delivery_Kind::KIND_E_DELIVERY;
	}
	

	public function setPaymentMethods( array $ids ) : void
	{
		foreach($this->payment_methods as $id=>$r) {
			if(!in_array($id, $ids)) {
				$r->delete();
				unset($this->delivery_classes[$id]);
			}
		}
		
		foreach( $ids as $id ) {
			
			if(!isset($this->payment_methods[$id])) {
				if( !Payment_Method::exists( $id ) ) {
					continue;
				}
				
				$new_item = new Delivery_Method_PaymentMethods();
				if($this->id) {
					$new_item->setDeliveryMethodId( $this->id );
				}
				$new_item->setPaymentMethodId( $id );
				
				$this->payment_methods[$id] = $new_item;
				if($this->id) {
					$new_item->save();
				}
			}
		}
	}

	public function getPaymentMethodsIds() : array
	{
		return array_keys( $this->payment_methods );
	}
	
}
