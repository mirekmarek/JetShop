<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;

use JetApplication\Entity_WithShopData;
use JetApplication\Payment_Kind;
use JetApplication\Payment_Method_ShopData;
use JetApplication\Payment_Method_Option;
use JetApplication\Payment_Method_Services;
use JetApplication\Delivery_Method;
use JetApplication\Payment_Method_DeliveryMethods;
use JetApplication\Services_Service;
use JetApplication\Payment_Method;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'payment_method',
	database_table_name: 'payment_methods'
)]
abstract class Core_Payment_Method extends Entity_WithShopData
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
		select_options_creator: [Payment_Kind::class, 'getScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select kind',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind'
		]
	)]
	protected string $kind = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Backend module:',
		select_options_creator: [Payment_Method::class, 'getModulesOptionsScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select module',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select module'
		]
	)]
	protected string $backend_module_name = '';
	
	
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
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		is_required: false,
		label: 'Delivery methods:',
		default_value_getter_name: 'getDeliveryMethodsIds',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select delivery method',
			Form_Field::ERROR_CODE_EMPTY => 'Please select delivery method'
		],
		select_options_creator: [Delivery_Method::class, 'getScope'],
	)]
	protected array $delivery_methods = [];

	/**
	 * @var Payment_Method_Services[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_Services::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		default_value_getter_name: 'getServicesIds',
		label: 'Services:',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select service',
		],
		select_options_creator: [Services_Service::class, 'getPaymentServicesScope'],
	)]
	protected array $services = [];
	
	/**
	 * @var Payment_Method_Option[]
	 */
	protected ?array $options = null;
	
	public function setKind( string $kind ): void
	{
		$this->kind = $kind;
		foreach(Shops::getList() as $shop) {
			$this->getShopData($shop)->setKind($kind);
		}
		
	}
	
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


	

	public function getShopData( ?Shops_Shop $shop=null ) : Payment_Method_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	
	
	public function setDeliveryMethods( array $ids ) : void
	{
		foreach($this->delivery_methods as $r) {
			if(!in_array($r->getDeliveryMethodId(), $ids)) {
				$r->delete();
				unset($this->delivery_methods[$r->getDeliveryMethodId()]);
			}
		}

		foreach( $ids as $id ) {
			if( !Delivery_Method::exists( $id ) ) {
				continue;
			}

			if(!isset($this->delivery_methods[$id])) {
				$new_item = new Payment_Method_DeliveryMethods();
				if($this->id) {
					$new_item->setPaymentMethodId($this->id);
				}
				$new_item->setDeliveryMethodId($id);

				$this->delivery_methods[$id] = $new_item;
				if($this->id) {
					$new_item->save();
				}
			}
		}
	}

	public function getDeliveryMethodsIds() : array
	{
		return array_keys($this->delivery_methods);
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
				
				$new_item = new Payment_Method_Services();
				if($this->id) {
					$new_item->setPaymentMethodId( $this->id );
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
	

	/**
	 * @param Shops_Shop $shop
	 *
	 * @return Payment_Method_Option[]
	 */
	public function getActiveOptions( Shops_Shop $shop ) : array
	{
		//TODO: refactor
		
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
	
	/**
	 * @return Payment_Method_Option[]
	 */
	public function getOptions() : array
	{
		if($this->options===null) {
			$this->options = Payment_Method_Option::getListForMethod( $this->id );
		}
		
		return $this->options;
	}
	
	
	public function addOption( Payment_Method_Option $option ) : void
	{
		$this->getOptions();
		
		$option->setPriority( count($this->options)+1 );
		$option->setMethodId( $this->id );
		$option->save();
		$this->options[$option->getId()] = $option;
		
		$option->activate();
		foreach(Shops::getList() as $shop) {
			if(!$option->getShopData($shop)->isActiveForShop()) {
				$option->getShopData($shop)->activate();
			}
		}
	}
	
	
	public function getOption( int $id ) : Payment_Method_Option|null
	{
		$this->getOptions();
		
		if(!isset($this->options[$id])) {
			return null;
		}
		
		return $this->options[$id];
	}
	
	
	public function sortOptions( array $sort ) : void
	{
		$this->getOptions();
		$i = 0;
		foreach($sort as $id) {
			if(isset($this->options[$id])) {
				$i++;
				$this->options[$id]->setPriority($i);
				$this->options[$id]->save();
			}
		}
	}
	
	public static function getModulesScope() : array
	{
		$scope = [''=>''];
		
		$modules = Application_Modules::activatedModulesList();
		
		foreach($modules as $manifest) {
			if(!str_starts_with($manifest->getName(), 'Payment.')) {
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
