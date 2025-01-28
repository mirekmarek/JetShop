<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;

use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_DeliveryMethods;
use JetApplication\Carrier;
use JetApplication\Delivery_Method;
use JetApplication\Delivery_Method_Price;
use JetApplication\Entity_Admin_WithEShopData_Interface;
use JetApplication\Entity_Admin_WithEShopData_Trait;
use JetApplication\Entity_Basic;
use JetApplication\Entity_HasImages_Interface;
use JetApplication\Entity_HasPrice_Interface;
use JetApplication\Entity_HasPrice_Trait;
use JetApplication\Entity_WithEShopData;
use JetApplication\Entity_WithEShopData_HasImages_Trait;
use JetApplication\Entity_Definition;
use JetApplication\Pricelist;
use JetApplication\EShop;
use JetApplication\Delivery_Kind;
use JetApplication\Delivery_Class;
use JetApplication\Delivery_Method_Class;
use JetApplication\Delivery_Method_PaymentMethods;
use JetApplication\Payment_Method;
use JetApplication\Delivery_Method_EShopData;
use JetApplication\EShops;
use JetApplication\Pricelists;
use JetApplication\Timer_Action;
use JetApplication\Timer_Action_SetPrice;

#[DataModel_Definition(
	name: 'delivery_method',
	database_table_name: 'delivery_methods',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_DeliveryMethods::class,
	images: [
		'icon1' => 'Icon 1',
		'icon2' => 'Icon 2',
		'icon3' => 'Icon 3',
	]
)]
abstract class Core_Delivery_Method extends Entity_WithEShopData implements
	Entity_HasPrice_Interface,
	Entity_HasImages_Interface,
	Entity_Admin_WithEShopData_Interface
{
	use Entity_WithEShopData_HasImages_Trait;
	use Entity_HasPrice_Trait;
	use Entity_Admin_WithEShopData_Trait;
	
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
		return
			$this->kind == Delivery_Kind::PERSONAL_TAKEOVER_EXTERNAL ||
			$this->kind == Delivery_Kind::PERSONAL_TAKEOVER_INTERNAL;
	}
	
	public function isInternalPersonalTakeover() : bool
	{
		return $this->kind == Delivery_Kind::PERSONAL_TAKEOVER_INTERNAL;
	}
	
	public function isExternalPersonalTakeover() : bool
	{
		return $this->kind == Delivery_Kind::PERSONAL_TAKEOVER_EXTERNAL;
	}

	public function isEDelivery() : bool
	{
		return $this->kind == Delivery_Kind::E_DELIVERY;
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
	
	
	protected ?Form $set_price_form = null;
	
	public function setupEditForm( Form $form ): void
	{
		$carrier_code = $form->field('carrier_code')->getValueRaw();
		$services = [''=>''];
		$delivery_point_types = [];
		
		if($carrier_code) {
			$carrier = Carrier::get( $carrier_code );
			
			if($carrier) {
				foreach($carrier->getServicesList() as $k=>$v) {
					$services[$k] = $v;
				}
				foreach($carrier->getDeliveryPointTypeOptions() as $k=>$v) {
					$delivery_point_types[$k] = $v;
				}
			}
		}
		
		
		/**
		 * @var Form_Field_Select $services_field
		 * @var Form_Field_Select $dp_type_field
		 */
		foreach( EShops::getList() as $eshop) {
			$services_field = $form->getField('/eshop_data/'.$eshop->getKey().'/carrier_service_code');
			$services_field->setIsRequired( false );
			$services_field->setSelectOptions( $services );
			
			$dp_type_field = $form->getField('/eshop_data/'.$eshop->getKey().'/allowed_delivery_point_types');
			$dp_type_field->setIsRequired( false );
			$dp_type_field->setSelectOptions( $delivery_point_types );
			
		}
	}
	
	
	public function getAddForm(): Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
		}
		
		return $this->_add_form;
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
				public function perform( Entity_Basic|Entity_HasPrice_Interface $entity, mixed $action_context ): bool
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
			$set_free_limit = new class( $eshop, $this->getEshopData($eshop)->getFreeDeliveryLimit() ) extends Timer_Action {
				protected float $free_delivery_limit;
				protected EShop $eshop;
				
				public function __construct( EShop $eshop, float $free_delivery_limit ) {
					$this->eshop = $eshop;
					$this->free_delivery_limit = $free_delivery_limit;
				}
				
				public function perform( Entity_Basic $entity, mixed $action_context ): bool
				{
					/**
					 * @var Delivery_Method $entity
					 */
					$sd = $entity->getEshopData( $this->eshop );
					$sd->setFreeDeliveryLimit( (float)$action_context );
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
