<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\Mvc_Page;
use Jet\Session;

abstract class Core_CashDesk
{
	const SESSION_NAME = 'cash_desk';

	const STEP_DELIVERY = 'delivery';
	const STEP_PAYMENT = 'payment';
	const STEP_CUSTOMER = 'customer';
	const STEP_CONFIRM = 'confirm';


	use CashDesk_Delivery;
	use CashDesk_Payment;
	use CashDesk_Customer;
	use CashDesk_Confirm;
	use CashDesk_ProcessControl;
	use CashDesk_Order;

	protected static string $module_name = 'Shop.CashDesk';

	protected static string $cash_desk_page_id = 'cash-desk';

	protected static ?CashDesk $cash_desk = null;


	protected string $shop_code = '';

	protected ?Session $session = null;

	protected bool $billing_address_editable = false;

	protected bool $delivery_address_editable = false;


	public static function getCashDeskPageId(): string
	{
		return static::$cash_desk_page_id;
	}

	public static function setCashDeskPageId( string $cash_desk_page_id ): void
	{
		static::$cash_desk_page_id = $cash_desk_page_id;
	}

	public static function getCashDeskPage(): Mvc_Page
	{
		$shop = Shops::getCurrent();

		return Mvc_Page::get(CashDesk::getCashDeskPageId(), $shop->getLocale(), $shop->getSiteId());
	}

	public static function getCashDeskPageURL(): string
	{
		return CashDesk::getCashDeskPage()->getURL();
	}

	public static function getModuleName(): string
	{
		return static::$module_name;
	}

	public static function setModuleName( string $module_name ): void
	{
		static::$module_name = $module_name;
	}


	public static function get() : CashDesk
	{
		if(!static::$cash_desk) {
			static::$cash_desk = new CashDesk( Shops::getCurrentCode() );
		}

		return static::$cash_desk;
	}

	public function __construct( string $shop_code )
	{
		$this->shop_code = $shop_code;
	}

	public function getShopCode() : string
	{
		return $this->shop_code;
	}

	protected function getSession() : Session
	{
		if(!$this->session) {
			$this->session = new Session(CashDesk::SESSION_NAME);
		}

		return $this->session;
	}

	public function getModule() : CashDesk_Module|Application_Module
	{
		return Application_Modules::moduleInstance( CashDesk::getModuleName() );
	}

}