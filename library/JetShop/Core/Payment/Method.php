<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;

use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_PaymentMethods;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasPrice_Interface;
use JetApplication\EShopEntity_HasPrice_Trait;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\Managers;
use JetApplication\Payment_Kind;
use JetApplication\Payment_Method_Module;
use JetApplication\Payment_Method_EShopData;
use JetApplication\Payment_Method_Option;
use JetApplication\Delivery_Method;
use JetApplication\Payment_Method_DeliveryMethods;
use JetApplication\Pricelist;
use JetApplication\Payment_Method;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Payment_Method_Price;
use JetApplication\Pricelists;
use JetApplication\Timer_Action;
use JetApplication\Timer_Action_SetPrice;

#[DataModel_Definition(
	name: 'payment_method',
	database_table_name: 'payment_methods'
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Payment method',
	admin_manager_interface: Admin_Managers_PaymentMethods::class,
	separate_tab_form_shop_data: false,
	images: [
		'icon1' => 'Icon 1',
		'icon2' => 'Icon 2',
		'icon3' => 'Icon 3',
	]
)]
abstract class Core_Payment_Method extends EShopEntity_WithEShopData implements
	EShopEntity_HasImages_Interface,
	EShopEntity_HasPrice_Interface,
	EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_WithEShopData_HasImages_Trait;
	use EShopEntity_HasPrice_Trait;
	use EShopEntity_Admin_WithEShopData_Trait;
	
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
	 * @var Payment_Method_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_EShopData::class
	)]
	protected array $eshop_data = [];


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
	
	public function getPriceEntity( Pricelist $pricelist ) : Payment_Method_Price
	{
		return Payment_Method_Price::get( $pricelist, $this->getId() );
	}
	
	
	public function setKind( string $kind ): void
	{
		$this->kind = $kind;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData($eshop)->setKind($kind);
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
	
	public function getBackendModule() : null|Payment_Method_Module|Application_Module
	{
		if(!$this->backend_module_name) {
			return null;
		}
		
		return Application_Modules::moduleInstance( $this->backend_module_name );
	}
	
	
	public function setBackendModuleName( string $backend_module_name ): void
	{
		$this->backend_module_name = $backend_module_name;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->setBackendModuleName( $backend_module_name );
		}
		$this->actualizeOptions();
	}
	
	public function getBackendModulePaymentMethodSpecification(): string
	{
		return $this->backend_module_payment_method_specification;
	}
	
	public function setBackendModulePaymentMethodSpecification( string $backend_module_payment_method_specification ): void
	{
		$this->backend_module_payment_method_specification = $backend_module_payment_method_specification;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->setBackendModulePaymentMethodSpecification( $backend_module_payment_method_specification );
		}
		$this->actualizeOptions();
	}


	

	public function getEshopData( ?EShop $eshop=null ) : Payment_Method_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
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
	
	public function actualizeOptions() : void
	{
		$backend = $this->getBackendModule();
		if(!$backend) {
			return;
		}
		
		/**
		 * @var Payment_Method $this
		 */
		
		$module_options = $backend->getPaymentMethodOptionsList( $this );
		
		$current_options = $this->getOptions();
		
		foreach( $current_options as $c_option ) {
			$code = $c_option->getInternalCode();
			if(!isset($module_options[$code])) {
				$c_option->delete();
				unset( $current_options[$code] );
				unset( $this->options[$code] );
			}
		}
		
		foreach($module_options as $code=>$title) {
			if( isset($this->options[$code]) ) {
				continue;
			}
			
			$option = new Payment_Method_Option();
			$option->setInternalCode( $code );
			$option->setInternalName( $title );
			$option->checkShopData();
			
			foreach( EShops::getList() as $eshop ) {
				$sd = $option->getEshopData( $eshop );
				$sd->setTitle( $title );
			}
			
			$this->addOption( $option );
		}

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
		$this->options[$option->getInternalCode()] = $option;
		
		$option->activate();
		foreach( EShops::getList() as $eshop) {
			if(!$option->getEshopData($eshop)->isActiveForShop()) {
				$option->getEshopData($eshop)->_activate();
			}
		}
	}
	
	
	public function getOption( string $code ) : Payment_Method_Option|null
	{
		$this->getOptions();
		
		if(!isset($this->options[$code])) {
			return null;
		}
		
		return $this->options[$code];
	}
	
	
	public function sortOptions( array $sort ) : void
	{
		$this->getOptions();
		$i = 0;
		foreach($sort as $code) {
			if(isset($this->options[$code])) {
				$i++;
				$this->options[$code]->setPriority($i);
				$this->options[$code]->save();
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
	
	
	
	
	protected ?Form $set_price_form = null;
	
	public function getAddForm(): Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
		}
		
		return $this->_add_form;
	}
	
	
	public function setupEditForm( Form $form ): void
	{
		$module_name = $form->field('backend_module_name')->getValueRaw();
		
		if($module_name) {
			/**
			 * @var Payment_Method_Module $module
			 */
			if(Application_Modules::moduleExists($module_name)) {
				$module = Application_Modules::moduleInstance( $module_name );
				$specification = $module->getPaymentMethodSpecificationList();
				
				/**
				 * @var Form_Field_Select $options
				 */
				$options = $form->getField('backend_module_payment_method_specification');
				$options->setSelectOptions( $specification );
			}
		} else {
			/**
			 * @var Form_Field_Select $spec
			 */
			$spec = $form->getField('backend_module_payment_method_specification');
			$spec->setIsRequired(false);
			$spec->setSelectOptions([''=>'']);
		}
		
	}
	
	public function catchAddForm(): bool
	{
		$form = $this->getAddForm();
		if(!$form->catchInput()) {
			return false;
		}
		
		$this->setupEditForm( $form );
		
		if(!$form->validate()) {
			return false;
		}
		
		$form->catch();
		
		return true;
	}
	
	public function catchEditForm(): bool
	{
		$form = $this->getEditForm();
		if(!$form->catchInput()) {
			return false;
		}
		
		$this->setupEditForm( $form );
		
		if(!$form->validate()) {
			return false;
		}
		
		$form->catch();
		
		return true;
	}
	
	public function getSetPriceForm() : ?Form
	{
		if( !static::getAdminManager()::getCurrentUserCanSetPrice() ) {
			return null;
		}
		
		if(!$this->set_price_form) {
			$this->set_price_form = new Form('set_price_form', []);
			
			
			foreach(Pricelists::getList() as $pricelist) {
				
				$field_name_prefix = '/'.$pricelist->getCode().'/';
				
				$vat_rate = new Form_Field_Select( $field_name_prefix.'vat_rate', 'VAT rate:' );
				$vat_rate->setDefaultValue( $this->getVatRate( $pricelist ) );
				
				$vat_rate->setErrorMessages([
					Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
				]);
				$vat_rate->setFieldValueCatcher(function( $value ) use ($pricelist) {
					$p = $this->getPriceEntity($pricelist);
					$p->setVatRate($value);
					$p->save();
				});
				$vat_rate->setSelectOptions( $pricelist->getVatRatesScope() );
				$this->set_price_form->addField( $vat_rate );
				
				
				$price = new Form_Field_Float($field_name_prefix.'default_price', 'Price:');
				$price->setDefaultValue( $this->getPrice( $pricelist ) );
				$price->setFieldValueCatcher(function( $value ) use ($pricelist) {
					$p = $this->getPriceEntity($pricelist);
					$p->setPrice($value);
					$p->save();
				});
				
				$this->set_price_form->addField( $price );
				
				
			}
			
		}
		
		return $this->set_price_form;
	}
	
	/**
	 * @return Timer_Action[]
	 */
	public function getAvailableTimerActions() : array
	{
		$actions = parent::getAvailableTimerActions();
		
		
		foreach(Pricelists::getList() as $pricelist ) {
			$set_price = new class( $pricelist, $this->getPrice( $pricelist ) ) extends Timer_Action_SetPrice {
				public function perform( EShopEntity_Basic|EShopEntity_HasPrice_Interface $entity, mixed $action_context ): bool
				{
					$p = $entity->getPriceEntity( $this->pricelist );
					$p->setPrice( (float)$action_context );
					$p->save();
					
					return true;
				}
			};
			
			$actions[$set_price->getAction()] = $set_price;
		}
		
		
		foreach(EShops::getListSorted() as $eshop) {
			$set_free_limit = new class( $eshop, $this->getEshopData($eshop)->getFreePaymentLimit() ) extends Timer_Action {
				protected float $free_delivery_limit;
				protected EShop $eshop;
				
				public function __construct( EShop $eshop, float $free_delivery_limit ) {
					$this->eshop = $eshop;
					$this->free_delivery_limit = $free_delivery_limit;
				}
				
				public function perform( EShopEntity_Basic $entity, mixed $action_context ): bool
				{
					/**
					 * @var Payment_Method $entity
					 */
					$sd = $entity->getEshopData($this->eshop);
					$sd->setFreePaymentLimit( (float)$action_context );
					$sd->save();
					
					return true;
				}
				
				public function getAction(): string
				{
					return 'set_free_limit:'.$this->eshop->getKey();
				}
				
				public function getTitle(): string
				{
					return Tr::_('Set free limit - %ESHOP%', ['ESHOP'=>$this->eshop->getName()]);
				}
				
				public function updateForm( Form $form ): void
				{
					$price = new Form_Field_Float('free_limit', 'Free limit:');
					$price->setDefaultValue( $this->free_delivery_limit );
					
					$form->addField( $price );
				}
				
				public function catchActionContextValue( Form $form ) : mixed
				{
					return $form->field('free_limit')->getValue();
				}
				
				public function formatActionContextValue( mixed $action_context ) : string
				{
					return Admin_Managers::PriceFormatter()->formatWithCurrency(
						$this->eshop->getDefaultPricelist(), (float)$action_context
					);
				}
				
			};
			
			$actions[$set_free_limit->getAction()] = $set_free_limit;
			
		}
		
		
		return $actions;
	}
	
}
