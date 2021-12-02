<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Exception;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Form_Field_MultiSelect;
use Jet\Form_Field_Select;
use Jet\Tr;

/**
 *
 */
#[DataModel_Definition(
	name: 'payment_method',
	database_table_name: 'payment_methods',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Payment_Method extends DataModel
{
	protected static string $manage_module_name = 'Admin.Payment.Methods';
	protected static string $method_module_name_prefix = 'Order.Payment.Methods.';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		form_field_type: 'Input',
		form_field_is_required: true,
		form_field_label: 'Code:',
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter code'
		]
	)]
	protected string $code = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: 'Input',
		form_field_is_required: true,
		form_field_label: 'Internal name:',
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter internal name'
		]
	)]
	protected string $internal_name = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
		form_field_type: 'Input',
		form_field_label: 'Internal description:'
	)]
	protected string $internal_description = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
		form_field_type: 'Select',
		form_field_is_required: true,
		form_field_label: 'Kind:',
		form_field_get_select_options_callback: [Payment_Kind::class, 'getScope'],
		form_field_error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select kind',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind'
		]
	)]
	protected string $kind = '';


	/**
	 * @var Payment_Method_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_ShopData::class
	)]
	protected array $shop_data = [];


	/**
	 * @var Payment_Method_DeliveryMethods[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_DeliveryMethods::class,
		form_field_creator_method_name: 'createDeliveryMethodInputField',
	)]
	protected array $delivery_methods = [];


	/**
	 * @var Payment_Method_Services[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_Services::class,
		form_field_creator_method_name: 'createServicesInputField',
	)]
	protected array $services = [];

	/**
	 * @var Payment_Method_Option[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_Option::class,
		form_field_type: false
	)]
	protected array $options = [];


	/**
	 * @var ?Form
	 */
	protected ?Form $_form_edit = null;

	/**
	 * @var ?Form
	 */
	protected ?Form $_form_add = null;

	protected static ?array $scope = null;

	/**
	 * @return string
	 */
	public static function getManageModuleName(): string
	{
		return static::$manage_module_name;
	}

	/**
	 * @param string $name
	 */
	public static function setManageModuleName( string $name ): void
	{
		static::$manage_module_name = $name;
	}

	/**
	 * @return string
	 */
	public static function getMethodModuleNamePrefix(): string
	{
		return static::$method_module_name_prefix;
	}

	/**
	 * @param string $method_module_name_prefix
	 */
	public static function setMethodModuleNamePrefix( string $method_module_name_prefix ): void
	{
		static::$method_module_name_prefix = $method_module_name_prefix;
	}



	public function __construct()
	{
		parent::__construct();

		$this->afterLoad();
	}

	public function afterLoad() : void
	{
		Payment_Method_ShopData::checkShopData( $this, $this->shop_data );
	}


	/**
	 * @return Form
	 */
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->getCommonForm('edit_form');
			$this->_form_edit->getField('code')->setIsReadonly(true);
		}

		return $this->_form_edit;
	}

	/**
	 * @return bool
	 */
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	/**
	 * @return Form
	 */
	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->getCommonForm('add_form');

			$code = $this->_form_add->getField('code');

			$code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if(!$value) {
					$field->setError( Form_Field_Input::ERROR_CODE_EMPTY );
					return false;
				}

				$exists = Payment_Method::get($value);

				if($exists) {
					$field->setCustomError(
						Tr::_('Payment method with the same name already exists')
					);

					return false;
				}

				return true;
			});
		}

		return $this->_form_add;
	}

	/**
	 * @return bool
	 */
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}

	/**
	 * @param string $code
	 * @return static|null
	 */
	public static function get( string $code ) : static|null
	{
		return static::load( $code );
	}

	/**
	 * @return iterable
	 */
	public static function getList() : iterable
	{
		$where = [];

		return static::fetchInstances( $where );
	}


	/**
	 * @param string $value
	 */
	public function setCode( string $value ) : void
	{
		$this->code = $value;
	}

	/**
	 * @return string
	 */
	public function getCode() : string
	{
		return $this->code;
	}


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

	public function getKind() : ?Payment_Kind
	{
		return Payment_Kind::get( $this->kind );
	}

	public function getKindTitle() : string
	{
		$kind = $this->getKind();
		return $kind ? $kind->getTitle() : '';
	}



	/**
	 * @param string $value
	 */
	public function setInternalDescription( string $value ) : void
	{
		$this->internal_description = $value;
	}

	/**
	 * @return string
	 */
	public function getInternalDescription() : string
	{
		return $this->internal_description;
	}

	/**
	 * @param string $value
	 */
	public function setInternalName( string $value ) : void
	{
		$this->internal_name = $value;
	}

	/**
	 * @return string
	 */
	public function getInternalName() : string
	{
		return $this->internal_name;
	}

	public function getShopData( ?Shops_Shop $shop=null ) : Payment_Method_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

	public function getEditURL() : string
	{
		return Payment_Method::getPaymentMethodEditURL( $this->getCode() );
	}

	public static function getPaymentMethodEditURL( string $code ) : string
	{
		/**
		 * @var Payment_Method_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Sticker::getManageModuleName() );

		return $module->getPaymentMethodEditURL( $code );
	}

	public static function getScope() : array
	{
		if(static::$scope===null) {
			$list = Payment_Method::getList();

			static::$scope = [];

			foreach($list as $item) {
				static::$scope[$item->getCode()] = $item->getInternalName();
			}
		}

		return static::$scope;
	}




	public function setDeliveryMethods( array $codes ) : void
	{
		foreach($this->delivery_methods as $r) {
			if(!in_array($r->getDeliveryMethodCode(), $codes)) {
				$r->delete();
				unset($this->delivery_methods[$r->getDeliveryMethodCode()]);
			}
		}

		foreach( $codes as $code ) {
			if( !($r = Delivery_Method::get( $code )) ) {
				continue;
			}

			if(!isset($this->delivery_methods[$r->getCode()])) {
				$new_item = new Payment_Method_DeliveryMethods();
				$new_item->setPaymentMethodCode($this->getCode());
				$new_item->setDeliveryMethodCode($code);

				$this->delivery_methods[$code] = $new_item;
				$new_item->save();
			}
		}
	}

	public function getDeliveryMethodsCodes() : array
	{
		return array_keys($this->getDeliveryMethods());
	}

	/**
	 *
	 * @return Delivery_Method[]
	 */
	public function getDeliveryMethods() : array
	{
		$res = [];
		foreach($this->delivery_methods as $code=>$item) {
			if(($item = $item->getDeliveryMethod())) {
				$res[$code] = $item;
			}
		}

		return $res;
	}

	public function createDeliveryMethodInputField() : Form_Field_MultiSelect
	{
		$input = new Form_Field_MultiSelect('delivery_methods', 'Delivery methods:', $this->getDeliveryMethodsCodes(), true );

		$input->setErrorMessages([
			Form_Field_MultiSelect::ERROR_CODE_INVALID_VALUE => 'Please select delivery method',
			Form_Field_MultiSelect::ERROR_CODE_EMPTY => 'Please select delivery method'
		]);

		$input->setSelectOptions(Delivery_Method::getScope());

		$input->setCatcher( function($value) {
			$this->setDeliveryMethods( $value );
		} );

		return $input;
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
			if( !($r = Services_Service::get( $code )) ) {
				continue;
			}

			if(!isset($this->services[$r->getCode()])) {
				$new_item = new Payment_Method_Services();
				$new_item->setPaymentMethodCode($this->getCode());
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
	public function getServices() : array
	{
		$res = [];
		foreach($this->services as $code=>$item) {
			if(($item = $item->getService())) {
				$res[$code] = $item;
			}
		}

		return $res;
	}

	public function createServicesInputField() : Form_Field_MultiSelect
	{
		$input = new Form_Field_MultiSelect('services', 'Services:', $this->getServicesCodes() );

		$input->setErrorMessages([
			Form_Field_MultiSelect::ERROR_CODE_INVALID_VALUE => 'Please select service',
		]);

		$input->setSelectOptions( Services_Service::getScope( Services_Kind::KIND_PAYMENT ) );

		$input->setCatcher( function($value) {
			$this->setServices( $value );
		} );

		return $input;
	}

	public function getOrderItem( CashDesk $cash_desk ) : Order_Item
	{
		$shd = $this->getShopData($cash_desk->getShop());

		$item = new Order_Item();
		$item->setType( Order_Item::ITEM_TYPE_PAYMENT );
		$item->setQuantity( 1 );
		$item->setTitle( $shd->getTitle() );
		$item->setCode( $this->getCode() );
		$item->setItemAmount( $shd->getDefaultPrice() );
		$item->setVatRate( $shd->getVatRate() );
		$item->setDescription( $shd->getDescriptionShort() );

		return $item;
	}


	/**
	 * @return Payment_Method_Option[]
	 */
	public function getOptions() : iterable
	{
		return $this->options;
	}


	public function getOption( string $code ) : Payment_Method_Option|null
	{
		if(!isset($this->options[$code])) {
			return null;
		}

		return $this->options[$code];
	}

	public function addOption( Payment_Method_Option $option ) : void
	{
		$this->options[$option->getCode()] = $option;
	}

	/**
	 * @param Shops_Shop $shop
	 *
	 * @return Payment_Method_Option[]
	 */
	public function getActiveOptions( Shops_Shop $shop ) : array
	{
		$res = [];

		foreach($this->options as $option) {
			$shd = $option->getShopData( $shop );
			if($shd->isActive()) {
				$res[$option->getCode()] = $option;
			}
		}

		uasort( $res, function( Payment_Method_Option $a, Payment_Method_Option $b ) use ($shop) {
			$p_a = $a->getShopData($shop)->getPriority();
			$p_b = $b->getShopData($shop)->getPriority();

			if(!$p_a<$p_b) {
				return -1;
			}

			if(!$p_a>$p_b) {
				return 1;
			}

			return 0;
		} );

		return $res;
	}

	public function getModuleName() : string
	{
		return Payment_Method::getMethodModuleNamePrefix().$this->getCode();
	}

	public function getModule(): ?Payment_Method_Module
	{
		$module_name = $this->getModuleName();

		if(Application_Modules::moduleExists($module_name)) {
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return Application_Modules::moduleInstance( $module_name );
		}

		if($this->getKind()->moduleIsRequired()) {
			throw new Exception('Payment module '.$module_name.' is required, but mussing (is not activated)');
		}

		return null;
	}

	public function getOrderStatusCode( CashDesk $cash_desk ) : string
	{

		$payment_method_module = $this->getModule();

		if(
			$payment_method_module &&
			($order_status_code = $payment_method_module->getOrderStatusCode( $cash_desk ) )
		) {
			return $order_status_code;
		}

		return $cash_desk->getShop()->getDefaultOrderStatusCode();
	}

	public function getOrderConfirmationEmailInfoText( Order $order ) : string
	{
		/**
		 * @var Payment_Method $this
		 */
		$module = $this->getModule();

		if($module) {
			return $module->getOrderConfirmationEmailInfoText( $order, $this );
		} else {
			$shop = $order->getShop();
			return $this->getShopData( $shop )->getConfirmationEmailInfoText();
		}
	}
}
