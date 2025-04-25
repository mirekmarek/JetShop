<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\DataModel_Query;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;

use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_DeliveryMethods;
use JetApplication\Carrier;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Carrier_Service;
use JetApplication\Delivery_Method;
use JetApplication\Delivery_Method_Price;
use JetApplication\EShop;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Common;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_HasPrice_Interface;
use JetApplication\EShopEntity_HasPrice_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasTimer_Interface;
use JetApplication\EShops;
use JetApplication\Order;
use JetApplication\Pricelist;
use JetApplication\Delivery_Kind;
use JetApplication\Delivery_Class;
use JetApplication\Delivery_Method_Class;
use JetApplication\Delivery_Method_PaymentMethods;
use JetApplication\Payment_Method;
use JetApplication\Product_EShopData;
use JetApplication\Timer_Action;
use JetApplication\Timer_Action_SetPrice;

#[DataModel_Definition(
	name: 'delivery_method',
	database_table_name: 'delivery_methods',
	relation: [
		'related_to_class_name' => Delivery_Method_Price::class,
		'join_by_properties' => [
			'id' => 'entity_id'
		],
		'join_type' => DataModel_Query::JOIN_TYPE_LEFT_OUTER_JOIN
	]
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Delivery method',
	admin_manager_interface: Admin_Managers_DeliveryMethods::class,
	images: [
		'icon1' => 'Icon 1',
		'icon2' => 'Icon 2',
		'icon3' => 'Icon 3',
	]
)]
abstract class Core_Delivery_Method extends EShopEntity_Common implements
	EShopEntity_HasPrice_Interface,
	EShopEntity_HasTimer_Interface,
	EShopEntity_HasImages_Interface,
	EShopEntity_Admin_Interface,
	EShopEntity_HasEShopRelation_Interface
{
	use EShopEntity_HasImages_Trait;
	use EShopEntity_HasPrice_Trait;
	use EShopEntity_Admin_Trait;
	use EShopEntity_HasEShopRelation_Trait;
	
	
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
		label: 'Delivery classes:',
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
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Length of delivery in working days:'
	)]
	protected int $length_of_delivery_in_working_days = 1;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon2 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon3 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	protected string $title = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 99999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Description:'
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Short description:'
	)]
	protected string $description_short = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Confirmation e-mail info text:'
	)]
	protected string $confirmation_email_info_text = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Order final page info text:'
	)]
	protected string $order_final_page_info_text = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:'
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Free delivery limit:'
	)]
	protected float $free_delivery_limit = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Discount is not allowed'
	)]
	protected bool $discount_is_not_allowed = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Carrier service:',
	)]
	protected string $carrier_service_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Allowed delivery point types:',
		is_required: false
	)]
	protected array $allowed_delivery_point_types = [];
	
	
	/**
	 * @var Delivery_Method_Price[]
	 */
	protected array $default_price = [];
	
	
	protected bool $enabled = true;
	
	
	
	
	public function getPriceEntity( Pricelist $pricelist ) : Delivery_Method_Price
	{
		return Delivery_Method_Price::get( $pricelist, $this->getId() );
	}
	
	
	public function setKind( string $code ): void
	{
		$this->kind = $code;
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
		return $this->getKind()->isPersonalTakeover();
	}
	
	public function isInternalPersonalTakeover() : bool
	{
		return $this->getKind()->isPersonalTakeoverInternal();
	}
	
	public function isExternalPersonalTakeover() : bool
	{
		return $this->getKind()->isPersonalTakeoverExternal();
	}

	public function isEDelivery() : bool
	{
		return $this->getKind()->isEDelivery();
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
			
			
			foreach($this->getEshop()->getPricelists() as $pricelist) {
				
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
		$actions = [];
		
		$activate = new class() extends Timer_Action {
			public function perform( EShopEntity_Basic $entity, mixed $action_context ): bool
			{
				$entity->activate();
				return true;
			}
			
			public function getAction(): string
			{
				return 'activate';
			}
			
			public function getTitle(): string
			{
				return Tr::_( 'Activate' );
			}
		};
		$actions[$activate->getAction()] = $activate;
		
		
		$deactivate = new class() extends Timer_Action {
			public function perform( EShopEntity_Basic $entity, mixed $action_context ): bool
			{
				$entity->deactivate();
				return true;
			}
			
			public function getAction(): string
			{
				return 'deactivate';
			}
			
			public function getTitle(): string
			{
				return Tr::_( 'Deactivate' );
			}
		};
		$actions[$deactivate->getAction()] = $deactivate;
		
		
		
		foreach( $this->getEshop()->getPricelists()  as $pricelist ) {
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
		
		

		$set_free_limit = new class( $this->getFreeDeliveryLimit() ) extends Timer_Action {
			protected float $free_delivery_limit;
			
			public function __construct( float $free_delivery_limit ) {
				$this->free_delivery_limit = $free_delivery_limit;
			}
			
			public function perform( EShopEntity_Basic $entity, mixed $action_context ): bool
			{
				/**
				 * @var Delivery_Method $entity
				 */
				$entity->setFreeDeliveryLimit( (float)$action_context );
				$entity->save();
				
				return true;
			}
			
			public function getAction(): string
			{
				return 'set_free_limit';
			}
			
			public function getTitle(): string
			{
				return Tr::_( 'Set free limit' );
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
		
		
		return $actions;
	}
	
	
	
	public function getEnabled(): bool
	{
		return $this->enabled;
	}
	
	public function setEnabled( bool $enabled ): void
	{
		$this->enabled = $enabled;
	}
	
	
	
	public function setIcon1( string $image ) : void
	{
		$this->image_icon1 = $image;
	}
	
	public function getIcon1() : string
	{
		return $this->image_icon1;
	}
	
	public function getIcon1ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl( 'icon1', $max_w, $max_h );
	}
	
	
	
	
	public function setIcon2( string $image ) : void
	{
		$this->image_icon2 = $image;
	}
	
	public function getIcon2() : string
	{
		return $this->image_icon2;
	}
	
	public function getIcon2ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl( 'icon2', $max_w, $max_h );
	}
	
	
	public function setIcon3( string $image ) : void
	{
		$this->image_icon3 = $image;
	}
	
	public function getIcon3() : string
	{
		return $this->image_icon3;
	}
	
	public function getIcon3ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl( 'icon3', $max_w, $max_h );
	}
	
	public function getLengthOfDeliveryInWorkingDays(): int
	{
		return $this->length_of_delivery_in_working_days;
	}
	
	public function setLengthOfDeliveryInWorkingDays( int $length_of_delivery_in_working_days ): void
	{
		$this->length_of_delivery_in_working_days = $length_of_delivery_in_working_days;
	}
	
	
	
	public function setTitle( string $value ) : void
	{
		$this->title = $value;
	}
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function setDescription( string $value ) : void
	{
		$this->description = $value;
	}
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	public function setDescriptionShort( string $value ) : void
	{
		$this->description_short = $value;
	}
	
	public function getDescriptionShort() : string
	{
		return $this->description_short;
	}
	
	public function getConfirmationEmailInfoText(): string
	{
		return $this->confirmation_email_info_text;
	}
	
	public function setConfirmationEmailInfoText( string $confirmation_email_info_text ): void
	{
		$this->confirmation_email_info_text = $confirmation_email_info_text;
	}
	
	public function getOrderFinalPageInfoText(): string
	{
		return $this->order_final_page_info_text;
	}
	
	public function setOrderFinalPageInfoText( string $order_final_page_info_text ): void
	{
		$this->order_final_page_info_text = $order_final_page_info_text;
	}
	
	
	public function setPriority( int $value ) : void
	{
		$this->priority = $value;
	}
	
	public function getPriority() : int
	{
		return $this->priority;
	}
	
	
	
	public function getDefaultPrice( Pricelist $pricelist ) : float
	{
		$code = $pricelist->getCode();
		
		if(!isset($this->default_price[$code])) {
			$this->default_price[$code] = clone $this->getPriceEntity( $pricelist );
		}
		return $this->default_price[$code]->getPrice();
	}
	
	public function setPrice( Pricelist $pricelist, float $price ): void
	{
		$this->getDefaultPrice( $pricelist );
		
		$this->getPriceEntity( $pricelist )->setPrice( $price );
	}
	
	
	public function setDiscountIsNotAllowed( bool $value ) : void
	{
		$this->discount_is_not_allowed = $value;
	}
	
	public function getDiscountIsNotAllowed() : bool
	{
		return $this->discount_is_not_allowed;
	}
	
	public function getFreeDeliveryLimit(): float
	{
		return $this->free_delivery_limit;
	}
	
	public function setFreeDeliveryLimit( float $free_delivery_limit ): void
	{
		$this->free_delivery_limit = $free_delivery_limit;
	}
	
	/**
	 * @param bool $only_active
	 * @return Carrier_DeliveryPoint[]
	 */
	public function getPersonalTakeoverDeliveryPoints( bool $only_active=true ) : array
	{
		if(!$this->isPersonalTakeover()) {
			return [];
		}
		
		$carrier = $this->getCarrier();
		if(!$carrier) {
			return [];
		}
		$locale = $this->getLocale();
		
		
		return Carrier_DeliveryPoint::getPointList(
			carrier: $carrier,
			only_types: $this->getAllowedDeliveryPointTypes(),
			only_locale: $locale,
			only_active: $only_active
		);
		
	}
	
	public function getPersonalTakeoverDeliveryPoint( string $place_code, $only_active=true ) : ?Carrier_DeliveryPoint
	{
		if(!$this->isPersonalTakeover()) {
			return null;
		}
		
		$carrier = $this->getCarrier();
		if(!$carrier) {
			return null;
		}
		
		
		$place = Carrier_DeliveryPoint::getPoint(
			$carrier,
			$place_code
		);
		
		if(!$place) {
			return null;
		}
		
		if($only_active && !$place->isActive()) {
			return null;
		}
		
		return $place;
	}
	
	public function hasPersonalTakeoverDeliveryPoint( string $place_code, $only_active=true ) : bool
	{
		return (bool)$this->getPersonalTakeoverDeliveryPoint( $place_code, $only_active );
	}
	
	
	public function getAllowedDeliveryPointTypes(): array
	{
		return $this->allowed_delivery_point_types;
	}
	
	public function setAllowedDeliveryPointTypes( array $allowed_delivery_point_types ): void
	{
		$this->allowed_delivery_point_types = $allowed_delivery_point_types;
	}
	
	
	
	public function getCarrier() : ?Carrier
	{
		if(!$this->carrier_code) {
			return null;
		}
		
		return Carrier::get($this->carrier_code);
	}
	
	public function getCarrierServiceCode(): string
	{
		return $this->carrier_service_code;
	}
	
	public function setCarrierServiceCode( string $carrier_service_code ): void
	{
		$this->carrier_service_code = $carrier_service_code;
	}
	
	
	public function getCarrierService(): ?Carrier_Service
	{
		return $this->getCarrier()?->getService( $this->carrier_service_code );
	}
	
	
	/**
	 * @return Payment_Method[]
	 */
	public function getPaymentMethods() : array
	{
		$ids = Delivery_Method_PaymentMethods::dataFetchCol(
			select:[
				'payment_method_id'
			],
			where: [
				'delivery_method_id' => $this->id
			]);
		
		return Payment_Method::getActiveList( $this->getEshop(), $ids, order_by: 'priority' );
	}
	
	public function generateConfirmationEmailInfoText( Order $order ) : string
	{
		$module = $this->getCarrier();
		
		if($module) {
			return $module->generateConfirmationEmailInfoText( $order, $this );
		} else {
			return $this->getConfirmationEmailInfoText();
		}
	}
	
	public function generateFinalPageInfoText( Order $order ) : string
	{
		$module = $this->getCarrier();
		
		if($module) {
			return $module->generateOrderFinalPageInfoText( $order, $this );
		} else {
			return $this->getOrderFinalPageInfoText();
		}
		
	}
	
	
	
	/**
	 * @param EShop $eshop
	 * @param Product_EShopData[] $products
	 *
	 * @return static[]
	 */
	public static function getAvailableByProducts( EShop $eshop, array $products ) : array
	{
		$delivery_classes = [];
		$delivery_kinds = [];
		
		$default_delivery_class = Delivery_Class::getDefault();
		
		foreach($products as $product ) {
			
			$delivery_class_id = $product->getDeliveryClassId();
			if(!$delivery_class_id) {
				if(!$default_delivery_class) {
					continue;
				}
				
				$delivery_class_id = $default_delivery_class->getId();
			}
			
			if(!isset($delivery_classes[$delivery_class_id])) {
				$delivery_class = Delivery_Class::load( $delivery_class_id );
				
				$delivery_classes[$delivery_class_id] = $delivery_class;
				
				foreach($delivery_class->getKinds() as $kind ) {
					$kind = $kind->getCode();
					
					if(!in_array($kind, $delivery_kinds)) {
						$delivery_kinds[] = $kind;
					}
					
				}
			}
		}
		
		$has_only_personal_takeover = false;
		$has_only_e_delivery = null;
		
		
		foreach( $delivery_classes as $delivery_class ) {
			if($delivery_class->isPersonalTakeOverOnly()) {
				$has_only_personal_takeover = true;
			}
			
			if($delivery_class->isEDelivery()) {
				if($has_only_e_delivery===null) {
					$has_only_e_delivery = true;
				}
			} else {
				$has_only_e_delivery = false;
			}
		}
		
		$available_delivery_methods = [];
		
		foreach($delivery_classes as $class ) {
			$methods = Delivery_Method::getActiveList( $eshop, $class->getDeliveryMethodIds(), order_by: 'priority' );
			
			foreach( $methods as $method ) {
				if(
					$has_only_personal_takeover &&
					!$method->getKind()->isPersonalTakeoverInternal()
				) {
					//There is something what is available only as "personal take over item" in the order. So only personal takeover methods are allowed
					continue;
				}
				
				if(
					$has_only_e_delivery &&
					!$method->getKind()->isEDelivery()
				) {
					//There is something virtual and nothing else. So only e-delivery is allowed
					continue;
				}
				
				if(
					!$has_only_e_delivery &&
					$method->getKind()->isEDelivery()
				) {
					//There is something physical. So e-delivery is not allowed
					continue;
				}
				
				$available_delivery_methods[$method->getId()] = $method;
				
			}
		}
		
		
		return $available_delivery_methods;
	}
	
	
	/**
	 * @param EShop $eshop
	 * @param array $ids
	 * @param array|string|null $order_by
	 * @return static[]
	 */
	public static function getActiveList( EShop $eshop, array $ids, array|string|null $order_by = null ) : array
	{
		if(!$ids) {
			return [];
		}
		
		$where = [
			$eshop->getWhere(),
			'AND',
			'id' => $ids,
			'AND',
			'is_active' => true
		];
		
		$_res =  static::fetch(
			where_per_model: [ ''=>$where],
			order_by: $order_by,
			item_key_generator: function( EShopEntity_Basic $item ) : int {
				return $item->getId();
			}
		);
		
		if($order_by) {
			return $_res;
		}
		
		$res = [];
		
		foreach($ids as $id) {
			if(isset($_res[$id])) {
				$res[$id] = $_res[$id];
			}
		}
		
		return $res;
	}
	
	
	/**
	 * @param EShop $eshop
	 * @param array|string|null $order_by
	 * @return static[]
	 */
	public static function getAllActive( EShop $eshop, array|string|null $order_by = null ) : array
	{
		
		$where = [
			$eshop->getWhere(),
			'AND',
			'is_active' => true
		];
		
		return static::fetch(
			where_per_model: [ ''=>$where],
			order_by: $order_by,
			item_key_generator: function( EShopEntity_Basic $item ) : int {
				return $item->getId();
			}
		);
	}
	
	
	protected function setupForm( Form $form ) : void
	{
		$eshop = new Form_Field_Select('eshop', 'e-shop');
		$eshop->setSelectOptions( EShops::getScope() );
		$eshop->setDefaultValue( $this->getEshop()->getKey() );
		$eshop->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$eshop->setFieldValueCatcher( function( string $eshop_key ) {
			$eshop = EShops::get( $eshop_key );
			$this->setEshop( $eshop );
		} );
		
		$form->addField( $eshop );
		
	}
	
	
	protected function setupAddForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	
	public function setupEditForm( Form $form ): void
	{
		$this->setupForm( $form );
		
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
		
		if(!$services) {
			$services = [''=>''];
		}
		
		$payment_methods_scope = [];
		foreach( Payment_Method::getList() as $payment_method ) {
			if($payment_method->getEshop()->getKey()==$this->getEshop()->getKey()) {
				$payment_methods_scope[$payment_method->getId()] = $payment_method->getInternalName();
			}
		}
		
		
		/**
		 * @var Form_Field_Select $carrier_service_code
		 * @var Form_Field_Select $allowed_delivery_point_types
		 * @var Form_Field_Select $payment_methods
		 */
		$carrier_service_code = $form->field('carrier_service_code');
		$carrier_service_code->setSelectOptions( $services );
		
		$allowed_delivery_point_types = $form->field('allowed_delivery_point_types');
		$allowed_delivery_point_types->setSelectOptions( $delivery_point_types );
		
		$payment_methods = $form->field('payment_methods');
		$payment_methods->setSelectOptions( $payment_methods_scope );
		
		
	}
	
	public static function getScopeForEShop( EShop $eshop ) : array
	{
		return static::dataFetchPairs(
			select: [
				'id',
				'internal_name'
			],
			where: $eshop->getWhere(),
			order_by: ['internal_name']
		);
	}
	
	/**
	 *
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListForEShop( EShop $eshop ) : DataModel_Fetch_Instances|iterable
	{
		return static::fetchInstances( $eshop->getWhere() );
	}
	
}
