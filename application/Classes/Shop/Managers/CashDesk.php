<?php
namespace JetApplication;

use Jet\MVC_Page_Interface;

interface Shop_Managers_CashDesk {
	
	public function get() : CashDesk;
	
	public function getCashDeskPageId(): string;
	public function getCashDeskPage(): MVC_Page_Interface;
	public function getCashDeskPageURL(): string;
	
	
	public function getCashDeskConfirmationPageId(): string;
	public function getCashDeskConfirmationPage(): MVC_Page_Interface;
	public function getCashDeskConfirmationPageURL(): string;
	
	public function getCashDeskPaymentPageId(): string;
	public function getCashDeskPaymentPage(): MVC_Page_Interface;
	public function getCashDeskPaymentPageURL(): string;
	
	public function getCashDeskPaymentProblemPageId(): string;
	public function getCashDeskPaymentProblemPage(): MVC_Page_Interface;
	public function getCashDeskPaymentProblemPageURL(): string;
	
	public function getCashDeskPaymentSuccessPageId(): string;
	public function getCashDeskPaymentSuccessPage(): MVC_Page_Interface;
	public function getCashDeskPaymentSuccessPageURL(): string;
	
	
	public function getCashDeskPaymentNotificationPageId(): string;
	public function getCashDeskPaymentNotificationPage(): MVC_Page_Interface;
	public function getCashDeskPaymentNotificationPageURL(): string;
	
	public function onCustomerLogin(): void;
	public function onCustomerLogout(): void;
	
}