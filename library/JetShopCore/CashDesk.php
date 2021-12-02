<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\MVC;
use Jet\MVC_Page_Interface;
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

	protected static string $cash_desk_confirmation_page_id = 'cash-desk-confirmation';

	protected static string $cash_desk_payment_page_id = 'cash-desk-payment';

	protected static string $cash_desk_payment_problem_page_id = 'cash-desk-payment-problem';

	protected static string $cash_desk_payment_success_page_id = 'cash-desk-payment-success';

	protected static string $cash_desk_payment_notification_page_id = 'cash-desk-payment-notification';

	protected static ?CashDesk $cash_desk = null;


	protected ?Shops_Shop $shop = null;

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

	public static function getCashDeskPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();

		return MVC::getPage(CashDesk::getCashDeskPageId(), $shop->getLocale(), $shop->getBaseId());
	}

	public static function getCashDeskPageURL(): string
	{
		return CashDesk::getCashDeskPage()->getURL();
	}




	public static function getCashDeskConfirmationPageId(): string
	{
		return self::$cash_desk_confirmation_page_id;
	}

	public static function setCashDeskConfirmationPageId( string $cash_desk_confirmation_page_id ): void
	{
		self::$cash_desk_confirmation_page_id = $cash_desk_confirmation_page_id;
	}

	public static function getCashDeskConfirmationPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();

		return MVC::getPage(CashDesk::getCashDeskConfirmationPageId(), $shop->getLocale(), $shop->getBaseId());
	}

	public static function getCashDeskConfirmationPageURL(): string
	{
		return CashDesk::getCashDeskConfirmationPage()->getURL();
	}





	public static function getCashDeskPaymentPageId(): string
	{
		return self::$cash_desk_payment_page_id;
	}

	public static function setCashDeskPaymentPageId( string $cash_desk_payment_page_id ): void
	{
		self::$cash_desk_payment_page_id = $cash_desk_payment_page_id;
	}

	public static function getCashDeskPaymentPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();

		return MVC::getPage(CashDesk::getCashDeskPaymentPageId(), $shop->getLocale(), $shop->getBaseId());
	}

	public static function getCashDeskPaymentPageURL(): string
	{
		return CashDesk::getCashDeskPaymentPage()->getURL();
	}



	public static function getCashDeskPaymentProblemPageId(): string
	{
		return self::$cash_desk_payment_problem_page_id;
	}

	public static function setCashDeskPaymentProblemPageId( string $cash_desk_payment_problem_page_id ): void
	{
		self::$cash_desk_payment_problem_page_id = $cash_desk_payment_problem_page_id;
	}

	public static function getCashDeskPaymentProblemPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();

		return MVC::getPage(CashDesk::getCashDeskPaymentProblemPageId(), $shop->getLocale(), $shop->getBaseId());
	}

	public static function getCashDeskPaymentProblemPageURL(): string
	{
		return CashDesk::getCashDeskPaymentProblemPage()->getURL();
	}




	public static function getCashDeskPaymentSuccessPageId(): string
	{
		return self::$cash_desk_payment_success_page_id;
	}

	public static function setCashDeskPaymentSuccessPageId( string $cash_desk_payment_success_page_id ): void
	{
		self::$cash_desk_payment_success_page_id = $cash_desk_payment_success_page_id;
	}

	public static function getCashDeskPaymentSuccessPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();

		return MVC::getPage(CashDesk::getCashDeskPaymentSuccessPageId(), $shop->getLocale(), $shop->getBaseId());
	}

	public static function getCashDeskPaymentSuccessPageURL(): string
	{
		return CashDesk::getCashDeskPaymentSuccessPage()->getURL();
	}



	public static function getCashDeskPaymentNotificationPageId(): string
	{
		return self::$cash_desk_payment_notification_page_id;
	}

	public static function setCashDeskPaymentNotificationPageId( string $cash_desk_payment_notification_page_id ): void
	{
		self::$cash_desk_payment_notification_page_id = $cash_desk_payment_notification_page_id;
	}

	public static function getCashDeskPaymentNotificationPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();

		return MVC::getPage(CashDesk::getCashDeskPaymentNotificationPageId(), $shop->getLocale(), $shop->getBaseId());
	}

	public static function getCashDeskPaymentNotificationPageURL(): string
	{
		return CashDesk::getCashDeskPaymentNotificationPage()->getURL();
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
			static::$cash_desk = new CashDesk( Shops::getCurrent() );
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

	public function getModule() : CashDesk_Module|Application_Module
	{
		return Application_Modules::moduleInstance( CashDesk::getModuleName() );
	}

}