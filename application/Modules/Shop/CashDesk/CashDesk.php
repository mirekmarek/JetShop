<?php
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Session;

use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\CashDesk as Application_CashDesk;

class CashDesk implements Application_CashDesk
{
	public const SESSION_NAME = 'cash_desk';

	public const STEP_DELIVERY = 'delivery';
	public const STEP_PAYMENT = 'payment';
	public const STEP_CUSTOMER = 'customer';
	public const STEP_CONFIRM = 'confirm';


	use CashDesk_Delivery;
	use CashDesk_Payment;
	use CashDesk_Customer;
	use CashDesk_Confirm;
	use CashDesk_ProcessControl;
	use CashDesk_Discounts;
	use CashDesk_Order;
	
	protected static ?CashDesk $cash_desk = null;


	protected ?Shops_Shop $shop = null;

	protected ?Session $session = null;

	protected bool $billing_address_editable = false;

	protected bool $delivery_address_editable = false;

	
	public static function get() : static
	{
		if(!static::$cash_desk) {
			static::$cash_desk = new static( Shops::getCurrent() );
		}

		return static::$cash_desk;
	}

	public function __construct( Shops_Shop $shop )
	{
		$this->shop = $shop;
	}

	public function getShop() : Shops_Shop
	{
		return $this->shop;
	}

	protected function getSession() : Session
	{
		if(!$this->session) {
			$this->session = new Session(CashDesk::SESSION_NAME);
		}

		return $this->session;
	}
	

}
