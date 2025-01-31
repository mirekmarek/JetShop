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
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_HasPrice_Interface;
use JetApplication\EShopEntity_HasPrice_Trait;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Order;
use JetApplication\Payment_Kind;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_Module;
use JetApplication\Payment_Method_Option_EShopData;
use JetApplication\Pricelist;
use JetApplication\Payment_Method_Price;

#[DataModel_Definition(
	name: 'payment_method_eshop_data',
	database_table_name: 'payment_methods_eshop_data',
	parent_model_class: Payment_Method::class
)]
abstract class Core_Payment_Method_EShopData extends EShopEntity_WithEShopData_EShopData implements
	EShopEntity_HasPrice_Interface,
	EShopEntity_HasImages_Interface
{
	use EShopEntity_HasPrice_Trait;
	use EShopEntity_HasImages_Trait;
	
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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $backend_module_payment_method_specification = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Free payment limit:'
	)]
	protected float $free_payment_limit = 0.0;
	
	/**
	 * @var Payment_Method_Option_EShopData[]
	 */
	protected ?array $options = null;
	
	
	/**
	 * @var Payment_Method_Price[]
	 */
	protected array $default_price = [];
	
	protected bool $enabled = true;
	
	
	public function getPriceEntity( Pricelist $pricelist ) : Payment_Method_Price
	{
		return Payment_Method_Price::get( $pricelist, $this->getId() );
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
	
	public function getKind() : ?Payment_Kind
	{
		return Payment_Kind::get( $this->kind );
	}
	
	public function getKindTitle() : string
	{
		$kind = $this->getKind();
		return $kind ? $kind->getTitle() : '';
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

	public function getFreePaymentLimit(): float
	{
		return $this->free_payment_limit;
	}
	
	public function setFreePaymentLimit( float $free_payment_limit ): void
	{
		$this->free_payment_limit = $free_payment_limit;
	}

	public function setConfirmationEmailInfoText( string $value ) : void
	{
		$this->confirmation_email_info_text = $value;
	}

	public function getConfirmationEmailInfoText() : string
	{
		return $this->confirmation_email_info_text;
	}
	
	public function getBackendModuleName(): string
	{
		return $this->backend_module_name;
	}
	
	public function setBackendModuleName( string $backend_module_name ): void
	{
		$this->backend_module_name = $backend_module_name;
	}
	
	public function getBackendModule() : null|Payment_Method_Module|Application_Module
	{
		if(!$this->backend_module_name) {
			return null;
		}
		
		return Application_Modules::moduleInstance( $this->backend_module_name );
	}
	
	public function getBackendModulePaymentMethodSpecification(): string
	{
		return $this->backend_module_payment_method_specification;
	}
	
	public function setBackendModulePaymentMethodSpecification( string $backend_module_payment_method_specification ): void
	{
		$this->backend_module_payment_method_specification = $backend_module_payment_method_specification;
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
	
	/**
	 * @return Payment_Method_Option_EShopData[]
	 */
	public function getOptions() : array
	{
		if($this->options===null) {
			$this->options = Payment_Method_Option_EShopData::getListForMethod( $this->entity_id, $this->getEshop() );
		}
		
		return $this->options;
	}
	
	/**
	 * @return Payment_Method_Option_EShopData[]
	 */
	public function getActiveOptions() : array
	{
		$options = [];
		
		foreach($this->getOptions() as $o) {
			if( $o->isActive() ) {
				$options[$o->getInternalCode()] = $o;
			}
		}
		
		return $options;
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

