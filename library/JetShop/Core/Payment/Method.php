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

use JetApplication\Entity_HasPrice_Interface;
use JetApplication\Entity_HasPrice_Trait;
use JetApplication\Entity_WithShopData;
use JetApplication\Managers;
use JetApplication\Payment_Kind;
use JetApplication\Payment_Method_Module;
use JetApplication\Payment_Method_ShopData;
use JetApplication\Payment_Method_Option;
use JetApplication\Delivery_Method;
use JetApplication\Payment_Method_DeliveryMethods;
use JetApplication\Pricelists_Pricelist;
use JetApplication\Payment_Method;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Payment_Method_Price;

#[DataModel_Definition(
	name: 'payment_method',
	database_table_name: 'payment_methods'
)]
abstract class Core_Payment_Method extends Entity_WithShopData implements Entity_HasPrice_Interface
{
	use Entity_HasPrice_Trait;
	
	protected static array $loaded = [];

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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Payment method specification:',
	)]
	protected string $backend_module_payment_method_specification = '';
	
	
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
	 * @var Payment_Method_Option[]
	 */
	protected ?array $options = null;
	
	public function getPriceEntity( Pricelists_Pricelist $pricelist ) : Payment_Method_Price
	{
		return Payment_Method_Price::get( $pricelist, $this->getId() );
	}
	
	
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
	
	public function getBackendModulePaymentMethodSpecification(): string
	{
		return $this->backend_module_payment_method_specification;
	}
	
	public function setBackendModulePaymentMethodSpecification( string $backend_module_payment_method_specification ): void
	{
		$this->backend_module_payment_method_specification = $backend_module_payment_method_specification;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setBackendModulePaymentMethodSpecification( $backend_module_payment_method_specification );
		}
	}


	

	public function getShopData( ?Shops_Shop $shop=null ) : Payment_Method_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
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
				$option->getShopData($shop)->_activate();
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
	
	/**
	 * @return Payment_Method_Module[]
	 */
	public static function getPaymentModules() : array
	{
		return Managers::findManagers(Payment_Method_Module::class, 'Payment.');
	}
	
	public static function getModulesScope() : array
	{
		$scope = [];
		
		foreach(static::getPaymentModules() as $module) {
			$manifest = $module->getModuleManifest();
			
			$scope[$manifest->getName()] = $manifest->getLabel().' ('.$manifest->getName().')';
		}
		
		return $scope;
	}
	
	public static function getModulesOptionsScope() : array
	{
		return [''=>'']+static::getModulesScope();
	}
}
