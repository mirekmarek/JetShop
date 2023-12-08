<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Exception;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;

use JetApplication\Entity_WithCodeAndShopData;
use JetApplication\Shops_Shop;
use JetApplication\Delivery_Kind;
use JetApplication\Delivery_Class;
use JetApplication\Delivery_Method_Class;
use JetApplication\Delivery_Method_PaymentMethods;
use JetApplication\Delivery_Method_Services;
use JetApplication\Payment_Method;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Services_Service;
use JetApplication\Delivery_Method;
use JetApplication\Shops;
use JetApplication\Delivery_PersonalTakeover_Place;
use JetApplication\CashDesk;
use JetApplication\Order;
use JetApplication\Order_Item;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_method',
	database_table_name: 'delivery_methods',
)]
abstract class Core_Delivery_Method extends Entity_WithCodeAndShopData
{
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
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please select delivery class',
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select delivery class'
		],
		default_value_getter_name: 'getDeliveryClassCodes',
		select_option_creator: [Delivery_Class::class, 'getScope']
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
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select payment method',
			Form_Field::ERROR_CODE_EMPTY => 'Please select payment method'
		],
		default_value_getter_name: 'getPaymentMethodsCodes',
		select_option_creator: [Payment_Method::class, 'getScope']
	)]
	protected array $payment_methods = [];

	

	/**
	 * @var Delivery_Method_Services[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_Services::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Services:',
		is_required: false,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select service',
		],
		default_value_getter_name: 'getServicesCodes',
		select_option_creator: [Services_Service::class, 'getDeliveryServicesScope']
	)]
	protected array $services = [];
	
	

	/**
	 * @var Delivery_Method_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];
	
	
	/**
	 * @param string $kind
	 */
	public function setKindCode( string $kind ): void
	{
		$this->kind = $kind;
	}

	/**
	 * @return string
	 */
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
		$kind = $this->getKind();
		return $kind ? $kind->getTitle() : '';
	}


	public function getShopData( ?Shops_Shop $shop=null ) : Delivery_Method_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

	/**
	 * @param array $codes
	 */
	public function setDeliveryClasses( array $codes ) : void
	{
		foreach($this->delivery_classes as $r) {
			if(!in_array($r->getClassCode(), $codes)) {
				$r->delete();
				unset($this->delivery_classes[$r->getClassCode()]);
			}
		}

		foreach( $codes as $code ) {
			if( !($r = Delivery_Class::get( $code )) ) {
				continue;
			}

			if(!isset($this->delivery_classes[$r->getCode()])) {
				$new_item = new Delivery_Method_Class();
				$new_item->setMethodCode($this->getCode());
				$new_item->setClassCode($code);

				$this->delivery_classes[$code] = $new_item;
				$new_item->save();
			}
		}
	}

	/**
	 *
	 * @return array
	 */
	public function getDeliveryClassCodes() : array
	{
		$codes = [];

		foreach($this->getDeliveryClasses() as $class) {
			$codes[] = $class->getCode();
		}

		return $codes;
	}

	/**
	 *
	 * @return Delivery_Class[]
	 */
	public function getDeliveryClasses() : iterable
	{
		$res = [];

		foreach( $this->delivery_classes as $code=>$rec ) {
			$rec = $rec->getClass();
			if($rec) {
				$res[$code] = $rec;
			}
		}

		return $res;
	}

	public function isPersonalTakeover() : bool
	{
		return $this->kind == Delivery_Kind::KIND_PERSONAL_TAKEOVER;
	}

	public function isEDelivery() : bool
	{
		return $this->kind == Delivery_Kind::KIND_E_DELIVERY;
	}


	public function getPersonalTakeoverPlace( Shops_Shop $shop, string $place_code, $only_active=true ) : ?Delivery_PersonalTakeover_Place
	{
		if(!$this->isPersonalTakeover()) {
			return null;
		}

		$place = Delivery_PersonalTakeover_Place::getPlace( $shop, $this->code, $place_code );

		if(!$place) {
			return null;
		}

		if($only_active && !$place->isActive()) {
			return null;
		}

		return $place;
	}

	public function hasPersonalTakeoverPlace( Shops_Shop $shop, string $place_code, $only_active=true ) : bool
	{
		return (bool)$this->getPersonalTakeoverPlace( $shop, $place_code, $only_active );
	}










	public function setPaymentMethods( array $codes ) : void
	{
		foreach($this->payment_methods as $r) {
			if(!in_array($r->getPaymentMethodCode(), $codes)) {
				$r->delete();
				unset($this->payment_methods[$r->getPaymentMethodCode()]);
			}
		}

		foreach( $codes as $code ) {
			if( !($r = Payment_Method::get( $code )) ) {
				continue;
			}

			if(!isset($this->payment_methods[$r->getCode()])) {
				$new_item = new Delivery_Method_PaymentMethods();
				$new_item->setDeliveryMethodCode($this->getCode());
				$new_item->setPaymentMethodCode($code);

				$this->payment_methods[$code] = $new_item;
				$new_item->save();
			}
		}
	}

	public function getPaymentMethodsCodes() : array
	{
		return array_keys( $this->getPaymentMethods() );
	}

	/**
	 *
	 * @return Payment_Method[]
	 */
	public function getPaymentMethods() : iterable
	{
		$res = [];

		foreach( $this->payment_methods as $code=>$item ) {
			if(($item = $item->getPaymentMethod())) {
				$res[$code] = $item;
			}
		}

		return $res;
	}









	public function setServices( array $codes ) : void
	{
		foreach($this->services as $r) {
			if(!in_array($r->getServiceCode(), $codes)) {
				$r->delete();
				unset($this->services[$r->getServiceCode()]);
			}
		}

		foreach( $codes as $code ) {
			if( !($r = Payment_Method::get( $code )) ) {
				continue;
			}

			if(!isset($this->services[$r->getCode()])) {
				$new_item = new Delivery_Method_Services();
				$new_item->setDeliveryMethodCode($this->getCode());
				$new_item->setServiceCode($code);

				$this->services[$code] = $new_item;
				$new_item->save();
			}
		}
	}

	public function getServicesCodes() : array
	{
		return array_keys($this->getServices());
	}

	/**
	 *
	 * @return Services_Service[]
	 */
	public function getServices() : iterable
	{
		$res = [];
		foreach($this->services as $code=>$item) {
			if(($item = $item->getService())) {
				$res[$code] = $item;
			}
		}

		return $res;
	}
	
	public function getOrderItem( CashDesk $cash_desk ) : Order_Item
	{
		$shd = $this->getShopData($cash_desk->getShop());

		$item = new Order_Item();
		$item->setType( Order_Item::ITEM_TYPE_DELIVERY );
		$item->setQuantity( 1 );
		$item->setTitle( $shd->getTitle() );
		$item->setCode( $this->getCode() );
		$item->setItemAmount( $shd->getDefaultPrice() );
		$item->setVatRate( $shd->getVatRate() );
		$item->setDescription( $shd->getDescriptionShort() );

		return $item;
	}

	public function getOrderConfirmationEmailInfoText( Order $order ) : string
	{
		/**
		 * @var Delivery_Method $this
		 */
		$module = $this->getModule();

		if($module) {
			return $module->getOrderConfirmationEmailInfoText( $order, $this );
		} else {
			return $this->getShopData( $order->getShop() )->getConfirmationEmailInfoText();
		}
	}

	public function getModuleName() : string
	{
		return Delivery_Method::getMethodModuleNamePrefix().$this->getCode();
	}

	public function getModule(): null|Core_Delivery_Method_Module|Core_Delivery_Method_Module_PersonalTakeover
	{
		$module_name = $this->getModuleName();

		if(Application_Modules::moduleExists($module_name)) {
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return Application_Modules::moduleInstance( $module_name );
		}

		if($this->getKind()->moduleIsRequired()) {
			throw new Exception('Delivery module '.$module_name.' is required, but mussing (is not activated)');
		}


		return null;
	}

}
