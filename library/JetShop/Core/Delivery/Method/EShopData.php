<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Carrier_Service;
use JetApplication\Delivery_Class;
use JetApplication\Delivery_Kind;
use JetApplication\Delivery_Method;
use JetApplication\Carrier;
use JetApplication\Delivery_Method_PaymentMethods;
use JetApplication\Delivery_Method_Price;
use JetApplication\Delivery_Method_EShopData;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_HasPrice_Interface;
use JetApplication\EShopEntity_HasPrice_Trait;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Order;
use JetApplication\Payment_Method_EShopData;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;


#[DataModel_Definition(
	name: 'delivery_method_eshop_data',
	database_table_name: 'delivery_methods_eshop_data',
	parent_model_class: Delivery_Method::class
)]
abstract class Core_Delivery_Method_EShopData extends EShopEntity_WithEShopData_EShopData implements
	EShopEntity_HasPrice_Interface,
	EShopEntity_HasImages_Interface
{
	use EShopEntity_HasImages_Trait;
	use EShopEntity_HasPrice_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $kind = '';
	
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
		is_key: true
	)]
	protected string $carrier_code = '';
	
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
	
	
	public function getEnabled(): bool
	{
		return $this->enabled;
	}
	
	public function setEnabled( bool $enabled ): void
	{
		$this->enabled = $enabled;
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
		$kind = $this->getKind();
		return $kind ? $kind->getTitle() : '';
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
	
	public function getCarrierCode(): string
	{
		return $this->carrier_code;
	}
	
	public function setCarrierCode( string $carrier_code ): void
	{
		$this->carrier_code = $carrier_code;
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
	 * @return Payment_Method_EShopData[]
	 */
	public function getPaymentMethods() : array
	{
		$ids = Delivery_Method_PaymentMethods::dataFetchCol(
			select:[
				'payment_method_id'
			],
			where: [
				'delivery_method_id' => $this->entity_id
			]);
		
		return Payment_Method_EShopData::getActiveList( $ids );
	}
	
	public function getOrderConfirmationEmailInfoText( Order $order ) : string
	{
		return $this->getConfirmationEmailInfoText();
	}
	
	
	
	/**
	 * @param Product_EShopData[] $products
	 *
	 * @return static[]
	 */
	public static function getAvailableByProducts( array $products ) : array
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
			$methods = Delivery_Method_EShopData::getActiveList( $class->getDeliveryMethodIds() );
			
			foreach( $methods as $method ) {
				if(
					$has_only_personal_takeover &&
					$method->getKindCode()!=Delivery_Kind::PERSONAL_TAKEOVER_INTERNAL
				) {
					//There is something what is available only as "personal take over item" in the order. So only personal takeover methods are allowed
					continue;
				}
				
				if(
					$has_only_e_delivery &&
					$method->getKindCode()!=Delivery_Kind::E_DELIVERY
				) {
					//There is something virtual and nothing else. So only e-delivery is allowed
					continue;
				}
				
				if(
					!$has_only_e_delivery &&
					$method->getKindCode()==Delivery_Kind::E_DELIVERY
				) {
					//There is something physical. So e-delivery is not allowed
					continue;
				}
				
				$available_delivery_methods[$method->getId()] = $method;
				
			}
		}
		
		
		return $available_delivery_methods;
	}
	
}
