<?php
namespace JetShop;

use Jet\Tr;

abstract class Core_Order_Modules_Payment_Abstract {

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

	protected bool $allow_discount = true;

	protected bool $allow_free = true;

	public function __construct( Order $order )
	{
		$this->order = $order;
		$this->vat_rate = Shops::getDefaultVatRate();

		$this->initialize();

	}

	public function getId() : string
	{
		return $this->id;
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


	abstract public function initialize() : void;

	public function isSelected() : bool
	{
		return $this->order->getPaymentMethodId()==$this->id;
	}

	public function onSelect() : void
	{
		//TODO:
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

	public function _( string $text, array $data=[] ) : string
	{
		return Tr::_( $text, $data, 'order.module.payment.'.$this->id );
	}









	public function getBannerHTMLCode() : string
	{
		return '';
	}

	public function getConfirmationMessage() : string
	{
		return '';
	}

	public function getConfirmationButtonLabel( string $default_text ) : string
	{
		return $default_text;
	}

	public function process() : void
	{
		//TODO:
	}

	public function getIsOnlinePayment() : bool
	{
		return false;
	}



	public function allowDiscount() : bool
	{
		return $this->allow_discount;
	}

	public function allowFree() : bool
	{
		return $this->allow_free;
	}

}