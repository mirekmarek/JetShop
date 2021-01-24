<?php
namespace JetShop;

use Jet\Tr;

abstract class Core_Order_Modules_Delivery_Abstract {

	protected ?Order $order = null;

	protected string $id = '';

	protected bool $visible = true;

	protected bool $enabled = true;

	protected bool $enabled_by_selection = true;

	protected float $standard_price = 0.0;

	protected float $price = 0.0;

	protected int $vat_rate = 0;

	protected string $title = '';

	protected string $description = '';

	protected string $icon = '';

	protected string $specification = '';

	protected array $allowed_payment_ids = [];

	protected string $default_payment_id = '';

	protected bool $is_default = false;

	protected bool $allow_discount = true;

	protected bool $allow_free = true;

	protected array $set_payment_prices = [];


	public function __construct( Order $order )
	{
		$this->order = $order;
		$this->vat_rate = Shops::getDefaultVatRate();

		$this->initialize();

	}

	abstract public function initialize() : void;

	public function isSelected() : bool
	{
		return $this->order->getDeliveryId()==$this->id;
	}

	public function onSelect() : void
	{
		//TODO:
	}

	public function getId() : string
	{
		return $this->id;
	}


	public function getIsDefault() : bool
	{
		return $this->is_default;
	}

	public function isPersonalPickup() : bool
	{
		return false;
	}

	public function isEDelivery() : bool
	{
		return false;
	}

	public function isAvailable() : bool
	{
		return $this->enabled && $this->enabled_by_selection;
	}

	public function isVisible() : bool
	{
		return $this->visible;
	}

	public function setVisible( bool $visible ) : void
	{
		$this->visible = $visible;
	}

	public function isEnabled() : bool
	{
		return $this->enabled;
	}

	public function setEnabled( bool $enabled ) : void
	{
		$this->enabled = $enabled;
	}

	public function getEnabledBySelection() : bool
	{
		return $this->enabled_by_selection;
	}

	public function setEnabledBySelection( bool $enabled_by_selection ) : void
	{
		$this->enabled_by_selection = $enabled_by_selection;
	}

	public function initPrice( float $price ) : void
	{
		$this->price = $price;
		$this->standard_price = $price;
	}

	public function setPrice( float $price ) : void
	{
		$this->price = $price;
	}

	public function getStandardPrice() : float
	{
		return $this->standard_price;
	}

	public function getPrice() : float
	{
		return $this->price;
	}

	public function getVatRate() : int
	{
		return $this->vat_rate;
	}

	public function getVatRateMultiplier() : float
	{
		return 1+($this->getVatRate()/100);
	}

	public function getSpecification() : string
	{
		return $this->specification;
	}

	public function setSpecification( string $specification ) : void
	{
		$this->specification = $specification;
	}

	public function getTitle() : string
	{
		return $this->title;
	}

	public function getName() : string
	{
		return $this->title;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function getIcon() : string
	{
		return $this->icon;
	}

	public function reset() : void
	{
	}

	public function isReady() : bool
	{
		return true;
	}

	public function getErrorMessage() : string
	{
		return '';
	}

	public function getOptionsHTML() : string
	{
		return '';
	}

	public function getHandlerHTML() : string
	{
		return '';
	}

	public function catchOptions() : bool
	{
		return true;
	}

	public function getSuccessPageInfo() : string
	{
		return '';
	}

	public function getConfirmationMailInfo() : string
	{
		return '';
	}

	public function forcedDeliveryAddress() : void
	{
	}

	public function allowDiscount() : bool
	{
		return $this->allow_discount;
	}

	public function allowFree() : bool
	{
		return $this->allow_free;
	}

	public function _( string $text, array $data=[] ) : string
	{
		return Tr::_( $text, $data, 'order.module.delivery.'.$this->id );
	}
}