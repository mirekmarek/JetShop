<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Application_Module;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use JetApplication\Shop_Managers_CashDesk;
use JetApplication\Shops;
use JetApplication\CashDesk as Application_CashDesk;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_CashDesk
{
	
	protected static string $cash_desk_page_id = 'cash-desk';
	
	protected static string $cash_desk_confirmation_page_id = 'cash-desk-confirmation';
	
	protected static string $cash_desk_payment_page_id = 'cash-desk-payment';
	
	protected static string $cash_desk_payment_problem_page_id = 'cash-desk-payment-problem';
	
	protected static string $cash_desk_payment_success_page_id = 'cash-desk-payment-success';
	
	protected static string $cash_desk_payment_notification_page_id = 'cash-desk-payment-notification';
	
	public function get() : Application_CashDesk
	{
		return CashDesk::get();
	}
	
	public function getCashDeskPageId(): string
	{
		return static::$cash_desk_page_id;
	}
	
	public function getCashDeskPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();
		
		return MVC::getPage($this->getCashDeskPageId(), $shop->getLocale(), $shop->getBaseId());
	}
	
	public function getCashDeskPageURL(): string
	{
		return $this->getCashDeskPage()->getURL();
	}
	
	
	
	
	public function getCashDeskConfirmationPageId(): string
	{
		return self::$cash_desk_confirmation_page_id;
	}
	
	public function getCashDeskConfirmationPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();
		
		return MVC::getPage($this->getCashDeskConfirmationPageId(), $shop->getLocale(), $shop->getBaseId());
	}
	
	public function getCashDeskConfirmationPageURL(): string
	{
		return $this->getCashDeskConfirmationPage()->getURL();
	}
	
	
	
	
	
	public function getCashDeskPaymentPageId(): string
	{
		return self::$cash_desk_payment_page_id;
	}
	
	public function getCashDeskPaymentPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();
		
		return MVC::getPage($this->getCashDeskPaymentPageId(), $shop->getLocale(), $shop->getBaseId());
	}
	
	public function getCashDeskPaymentPageURL(): string
	{
		return $this->getCashDeskPaymentPage()->getURL();
	}
	
	
	
	public function getCashDeskPaymentProblemPageId(): string
	{
		return self::$cash_desk_payment_problem_page_id;
	}
	
	public function getCashDeskPaymentProblemPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();
		
		return MVC::getPage($this->getCashDeskPaymentProblemPageId(), $shop->getLocale(), $shop->getBaseId());
	}
	
	public function getCashDeskPaymentProblemPageURL(): string
	{
		return $this->getCashDeskPaymentProblemPage()->getURL();
	}
	
	
	
	
	public function getCashDeskPaymentSuccessPageId(): string
	{
		return self::$cash_desk_payment_success_page_id;
	}
	
	public function getCashDeskPaymentSuccessPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();
		
		return MVC::getPage($this->getCashDeskPaymentSuccessPageId(), $shop->getLocale(), $shop->getBaseId());
	}
	
	public function getCashDeskPaymentSuccessPageURL(): string
	{
		return $this->getCashDeskPaymentSuccessPage()->getURL();
	}
	
	
	
	public function getCashDeskPaymentNotificationPageId(): string
	{
		return self::$cash_desk_payment_notification_page_id;
	}
	
	
	public function getCashDeskPaymentNotificationPage(): MVC_Page_Interface
	{
		$shop = Shops::getCurrent();
		
		return MVC::getPage($this->getCashDeskPaymentNotificationPageId(), $shop->getLocale(), $shop->getBaseId());
	}
	
	public function getCashDeskPaymentNotificationPageURL(): string
	{
		return static::getCashDeskPaymentNotificationPage()->getURL();
	}
	
	public function onCustomerLogin(): void
	{
		CashDesk::get()->onCustomerLogin();
	}
	
	public function onCustomerLogout(): void
	{
		CashDesk::get()->onCustomerLogout();
	}
	
}