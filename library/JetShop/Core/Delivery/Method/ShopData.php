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
use JetApplication\Delivery_Kind;
use JetApplication\Delivery_Method;
use JetApplication\Delivery_Method_Module;
use JetApplication\Delivery_Method_PaymentMethods;
use JetApplication\Delivery_PersonalTakeover_Place;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Order;
use JetApplication\Payment_Method_ShopData;


#[DataModel_Definition(
	name: 'delivery_method_shop_data',
	database_table_name: 'delivery_methods_shop_data',
	parent_model_class: Delivery_Method::class
)]
abstract class Core_Delivery_Method_ShopData extends Entity_WithShopData_ShopData
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $kind = '';
	
	
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
		label: 'Default price:'
	)]
	protected float $default_price = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'VAT rate:',
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select VAT rate',
		],
		creator: ['this', 'createVatRateInputField'],
	)]
	protected float $vat_rate = 0.0;

	
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
	protected string $backend_module_name = '';
	
	
	
	protected ?float $price = null;
	
	protected bool $enabled = true;
	
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
	
	public function setDefaultPrice( float $value ) : void
	{
		$this->default_price = $value;
	}

	public function getDefaultPrice() : float
	{
		return $this->default_price;
	}

	public function setVatRate( float $value ) : void
	{
		$this->vat_rate = $value;
	}
	
	public function getPrice(): ?float
	{
		if($this->price===null) {
			$this->price = $this->getDefaultPrice();
		}
		return $this->price;
	}
	
	public function setPrice( float $price ): void
	{
		$this->price = $price;
	}
	

	/**
	 * @return float
	 */
	public function getVatRate() : float
	{
		return $this->vat_rate;
	}

	public function createVatRateInputField( Form_Field_Select $input ) : Form_Field_Select
	{
		$shop = $this->getShop();
		
		$input->setDefaultValue( !$this->getIsSaved() ? $shop->getDefaultVatRate()  : $this->vat_rate );


		$vat_rates = $shop->getVatRates();

		$vat_rates = array_combine($vat_rates, $vat_rates);

		$input->setSelectOptions($vat_rates);

		return $input;
	}

	public function setDiscountIsNotAllowed( bool $value ) : void
	{
		$this->discount_is_not_allowed = $value;
	}
	
	public function getDiscountIsNotAllowed() : bool
	{
		return $this->discount_is_not_allowed;
	}
	
	public function isPersonalTakeover() : bool
	{
		return $this->kind == Delivery_Kind::KIND_PERSONAL_TAKEOVER;
	}
	
	public function isEDelivery() : bool
	{
		return $this->kind == Delivery_Kind::KIND_E_DELIVERY;
	}
	
	/**
	 * @param bool $only_active
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	public function getPersonalTakeoverPlaces( bool $only_active=true ) : array
	{
		if(!$this->isPersonalTakeover()) {
			return [];
		}
		
		return Delivery_PersonalTakeover_Place::getListForMethod( $this, $only_active );
		
	}
	
	/**
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	public function getPersonalTakeoverPlaceHashMap() : array
	{
		if(!$this->isPersonalTakeover()) {
			return [];
		}
		
		return Delivery_PersonalTakeover_Place::getHashMapForMethod( $this );
		
	}
	
	
	
	public function getPersonalTakeoverPlace( string $place_code, $only_active=true ) : ?Delivery_PersonalTakeover_Place
	{
		if(!$this->isPersonalTakeover()) {
			return null;
		}
		
		$place = Delivery_PersonalTakeover_Place::getPlace( $this, $place_code );
		
		if(!$place) {
			return null;
		}
		
		if($only_active && !$place->isActive()) {
			return null;
		}
		
		return $place;
	}
	
	public function hasPersonalTakeoverPlace( string $place_code, $only_active=true ) : bool
	{
		return (bool)$this->getPersonalTakeoverPlace( $place_code, $only_active );
	}
	
	public function getBackendModuleName(): string
	{
		return $this->backend_module_name;
	}
	
	public function setBackendModuleName( string $backend_module_name ): void
	{
		$this->backend_module_name = $backend_module_name;
	}
	
	public function getBackendModule() : null|Delivery_Method_Module|Application_Module
	{
		if(!$this->backend_module_name) {
			return null;
		}
		
		return Application_Modules::moduleInstance( $this->backend_module_name );
	}
	
	/**
	 * @return Payment_Method_ShopData[]
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
		
		return Payment_Method_ShopData::getActiveList( $ids );
	}
	
	public function getOrderConfirmationEmailInfoText( Order $order ) : string
	{
		$module = $this->getBackendModule();
		
		if($module) {
			return $module->getOrderConfirmationEmailInfoText( $order, $this );
		} else {
			return $this->getConfirmationEmailInfoText();
		}
	}
	
}
