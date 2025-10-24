<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\HomeCredit;

use JsonSerializable;
use stdClass;

class CreditApplication implements JsonSerializable {
	
	public const APPLICATION_CANCELLED_CARRIER_CHANGED      = 'APPLICATION_CANCELLED_CARRIER_CHANGED';      //Zm�na dodavatele zbo��
	public const APPLICATION_CANCELLED_CART_CONTENT_CHANGED = 'APPLICATION_CANCELLED_CART_CONTENT_CHANGED'; //Obsah n�kupn�ho ko��ku zm�n�n
	public const APPLICATION_CANCELLED_BY_CUSTOMER          = 'APPLICATION_CANCELLED_BY_CUSTOMER';          //Zru�eno z�kazn�kem (ve spr�v� jeho ��tu/objedn�vek)
	public const APPLICATION_CANCELLED_BY_ERP               = 'APPLICATION_CANCELLED_BY_ERP';               //Zru�eno na z�klad� back-office procesu obchodu (nap�. z d�vodu chyb�j�c� polo�ky zbo��)
	public const APPLICATION_CANCELLED_EXPIRED              = 'APPLICATION_CANCELLED_EXPIRED';              //Vypr�en� platnosti ��dosti/objedn�vky
	public const APPLICATION_CANCELLED_UNFINISHED           = 'APPLICATION_CANCELLED_UNFINISHED';           //Objedn�vka nebyla z�kazn�kem dokon�ena
	public const APPLICATION_CANCELLED_BY_ESHOP_RULES       = 'APPLICATION_CANCELLED_BY_ESHOP_RULES';       //Poru�en� vnit�n�ch pravidel e-shopu (nap�. z d�vodu neplatn�ch dodate�n�ch dat z�kazn�ka)
	public const APPLICATION_CANCELLED_OTHER                = 'APPLICATION_CANCELLED_OTHER';                //Jin� d�vod specifikovan� v polo�ce customReason
	
	
	public const STATE_PROCESSING_REDIRECT_NEEDED  = 'PROCESSING_REDIRECT_NEEDED'; //stav po p�esm�rov�n�, resp. po vytvo�en� createApplication, 1.krok
	public const STATE_PROCESSING_PREAPPROVED      = 'PROCESSING_PREAPPROVED';     //
	public const STATE_PROCESSING_APPROVED         = 'PROCESSING_APPROVED';        //
	public const STATE_PROCESSING_REVIEW           = 'PROCESSING_REVIEW';          //
	public const STATE_PROCESSING_ALT_OFFER        = 'PROCESSING_ALT_OFFER';       //
	public const STATE_PROCESSING_SIGNED           = 'PROCESSING_SIGNED';          //klient podepsal smlouvy, nyn� je p�esm�rov�n zp�t na eshop na adresu Url_approved
	
	public const STATE_READY_TO_SHIP      = 'READY_TO_SHIP';    //objedn�vka je p�ipravena k odesl�n� klientovi, zbo�� ode�lete nebo p�edejte klientovi
	
	public const STATE_READY_SHIPPED      = 'READY_SHIPPED';    //potvrzeno eshopem, �e zbo�� bylo odesl�no klientovi
	public const STATE_READY_DELIVERED    = 'READY_DELIVERED';  //potvrzeno eshopem, �e zbo�� bylo dod�no klientovi
	
	public const STATE_READY_PAID         = 'READY_PAID';       //
	
	public const STATE_REJECTED           = 'STATE_REJECTED';            //klient byl zam�tnut, nyn� je odesl�n zp�t na eshop na adresu url_rejected
	public const STATE_CANCELLED_NOT_PAID = 'STATE_CANCELLED_NOT_PAID';  //
	public const STATE_CANCELLED_RETURNED = 'STATE_CANCELLED_RETURNED';  //

	
	protected Config_PerShop $config;
	protected string $customer_firstName = '';
	protected string $customer_lastName = '';
	protected string $customer_email = '';
	protected string $customer_phone = '';
	
	/**
	 * @var CreditApplication_Address[]
	 */
	protected array $customer_addresses = [];
	protected string $order_number = '';
	protected array $order_variableSymbols = [];
	protected float $order_amount = 0.0;
	
	/**
	 * @var CreditApplication_OrderItem[]
	 */
	protected array $order_items = [];
	
	
	protected string $URL_approvedRedirect = '';
	protected string $URL_rejectedRedirect = '';
	protected string $URL_notificationEndpoint = '';
	protected bool $set_credit_params = false;


	protected int $cp_preferredMonths = 0;
	protected float $cp_preferredInstallmentAmoun = 0.0;
	protected float $cp_preferredDownPaymentAmount = 0.0;
	protected string $cp_productCode = '';
	protected string $cp_productSetCode = '';
	
	
	public function __construct( Config_PerShop $config )
	{
		$this->config = $config;
	}
	
	public function setCustomerFirstName( string $customer_firstName ) : void
	{
		$this->customer_firstName = $customer_firstName;
	}
	
	public function setCustomerLastName( string $customer_lastName ) : void
	{
		$this->customer_lastName = $customer_lastName;
	}
	
	public function setCustomerEmail( string $customer_email ) : void
	{
		$this->customer_email = $customer_email;
	}
	
	public function setCustomerPhone( string $customer_phone ) : void
	{
		$this->customer_phone = $customer_phone;
	}
	
	/**
	 * @param CreditApplication_Address $customer_address
	 */
	public function addCustomerAddress( CreditApplication_Address $customer_address ) : void
	{
		$this->customer_addresses[] = $customer_address;
	}
	
	public function setOrderNumber( string $order_number ) : void
	{
		$this->order_number = $order_number;
	}
	
	public function getOrderNumber() : string
	{
		return $this->order_number;
	}
	
	public function setOrderVariableSymbols( array $order_variableSymbols ) : void
	{
		$this->order_variableSymbols = $order_variableSymbols;
	}
	
	public function setOrderAmount( float $order_amount ) : void
	{
		$this->order_amount = $order_amount;
	}
	
	public function addOrderItem( CreditApplication_OrderItem $order_item ) : void
	{
		$order_item->setConfig( $this->config );
		$this->order_items[] = $order_item;
		
		$this->order_amount += $order_item->getAmount();
	}
	
	public function setURLApprovedRedirect( string $URL_approvedRedirect ) : void
	{
		$this->URL_approvedRedirect = $URL_approvedRedirect;
	}
	
	public function setURLRejectedRedirect( string $URL_rejectedRedirect ) : void
	{
		$this->URL_rejectedRedirect = $URL_rejectedRedirect;
	}
	
	public function setURLNotificationEndpoint( string $URL_notificationEndpoint ) : void
	{
		$this->URL_notificationEndpoint = $URL_notificationEndpoint;
	}
	
	public function setCreditparams(
		int $cp_preferredMonths,
		float $cp_preferredInstallmentAmount,
		float $cp_preferredDownPaymentAmount,
		string $cp_productCode,
		string $cp_productSetCode
	) : void
	{
		$this->set_credit_params = true;
		
		$this->cp_preferredMonths = $cp_preferredMonths;
		$this->cp_preferredInstallmentAmoun = $cp_preferredInstallmentAmount;
		$this->cp_preferredDownPaymentAmount = $cp_preferredDownPaymentAmount;
		$this->cp_productCode = $cp_productCode;
		$this->cp_productSetCode = $cp_productSetCode;
		
	}
	
	
	
	public function jsonSerialize() : array
	{
		$d = [
			'customer' => [
				'firstName' => $this->customer_firstName,
				'lastName'  => $this->customer_lastName,
				'email'     => $this->customer_email,
				'phone'     => $this->customer_phone,
				'addresses' => [],
			],
			'order' => [
				'number'          => $this->order_number,
				'variableSymbols' => $this->order_variableSymbols,
				'totalPrice' => [
					'amount'    => round($this->order_amount*10),
					'currency'  => $this->config->getEshop()->getDefaultPricelist()->getCurrency()->getCode(),
				],
				'items' => []
			],
			'agreementPersonalDataProcessing' => true,
			'merchantUrls' => [
				'approvedRedirect'     => $this->URL_approvedRedirect,
				'rejectedRedirect'     => $this->URL_rejectedRedirect,
				'notificationEndpoint' => $this->URL_notificationEndpoint
			],
			'type' => 'INSTALLMENT',
			'settingsInstallment' => new stdClass(),
		];
		
		foreach($this->customer_addresses as $customer_address) {
			$d['customer']['addresses'][] = $customer_address->jsonSerialize();
		}
		
		foreach($this->order_items as $order_item) {
			$d['order']['items'][] = $order_item->jsonSerialize();
		}
		

		/*
		if($this->set_credit_params) {
			$d['settingsInstallment'] = [
				'preferredMonths' => $this->cp_preferredMonths,
				'preferredInstallment' => [
					'amount' => $this->cp_preferredInstallmentAmoun,
					'currency' => $this->config->getEshop()->getDefaultPricelist()->getCurrency()->getCode(),
				],
				'preferredDownPayment' => [
					'amount' => $this->cp_preferredDownPaymentAmount,
					'currency' => $this->config->getEshop()->getDefaultPricelist()->getCurrency()->getCode(),
				],
				'productCode' => $this->cp_productCode,
				'productSetCode' => $this->cp_productSetCode
			];
		}
		*/
		
		return $d;
	}
	
	public function toJSON() : string
	{
		return json_encode( $this );
	}
}