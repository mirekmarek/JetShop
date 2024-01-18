<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;

use JetApplication\Delivery_Method_Module;
use JetApplication\Entity_WithShopData;
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

#[DataModel_Definition(
	name: 'delivery_method',
	database_table_name: 'delivery_methods',
)]
abstract class Core_Delivery_Method extends Entity_WithShopData
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
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select service',
		],
		default_value_getter_name: 'getServicesIds',
		select_options_creator: [Services_Service::class, 'getDeliveryServicesScope']
	)]
	protected array $services = [];
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Backend module:',
		select_options_creator: [Delivery_Method::class, 'getModulesOptionsScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select module',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select module'
		]
	)]
	protected string $backend_module_name = '';
	

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
	 * @param string $code
	 */
	public function setKind( string $code ): void
	{
		$this->kind = $code;
		foreach(Shops::getList() as $shop) {
			$this->getShopData($shop)->setKind($code);
		}
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
		return $this->getKind()?->getTitle()?:'';
	}
	
	public function getBackendModuleName(): string
	{
		return $this->backend_module_name;
	}
	
	public function setBackendModuleName( string $backend_module_name ): void
	{
		$this->backend_module_name = $backend_module_name;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setBackendModuleName( $backend_module_name );
		}
	}
	
	public function getBackendModule() : null|Delivery_Method_Module|Application_Module
	{
		if(!$this->backend_module_name) {
			return null;
		}
		
		return Application_Modules::moduleInstance( $this->backend_module_name );
	}
	
	

	public function getShopData( ?Shops_Shop $shop=null ) : Delivery_Method_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
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









	public function setServices( array $ids ) : void
	{
		foreach($this->services as $id=>$r) {
			if(!in_array($id, $ids)) {
				$r->delete();
				unset($this->services[$id]);
			}
		}
		
		foreach( $ids as $id ) {
			
			if(!isset($this->services[$id])) {
				if( !Services_Service::exists( $id ) ) {
					continue;
				}
				
				$new_item = new Delivery_Method_Services();
				if($this->id) {
					$new_item->setDeliveryMethodId( $this->id );
				}
				$new_item->setServiceId( $id );
				
				$this->services[$id] = $new_item;
				if($this->id) {
					$new_item->save();
				}
			}
		}
	}

	public function getServicesIds() : array
	{
		return array_keys($this->services);
	}
	
	
	public static function getModulesScope() : array
	{
		$scope = [''=>''];
		
		$modules = Application_Modules::activatedModulesList();
		
		foreach($modules as $manifest) {
			if(!str_starts_with($manifest->getName(), 'Delivery.')) {
				continue;
			}
			
			$scope[$manifest->getName()] = $manifest->getLabel().' ('.$manifest->getName().')';
		}
		
		return $scope;
	}
	
	public static function getModulesOptionsScope() : array
	{
		return [''=>'']+static::getModulesScope();
	}
	
}
