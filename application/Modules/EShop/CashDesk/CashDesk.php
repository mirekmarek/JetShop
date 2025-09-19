<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;


use Jet\Session;

use JetApplication\Availability;
use JetApplication\Pricelist;
use JetApplication\EShop;
use JetApplication\CashDesk as Application_CashDesk;
use JetApplication\ShoppingCart;

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
	
	
	protected ShoppingCart $cart;
	protected Config_PerShop $config;
	protected EShop $eshop;
	protected Pricelist $pricelist;
	protected Availability $availability;

	protected ?Session $session = null;

	protected bool $billing_address_editable = false;

	protected bool $delivery_address_editable = false;
	

	public function __construct(
		Config_PerShop              $config,
		ShoppingCart                $cart
	) {
		$this->config = $config;
		$this->cart = $cart;
		$this->eshop = $cart->getEshop();
		$this->pricelist = $cart->getPricelist();
		$this->availability = $cart->getAvailability();
	}
	
	public function getConfig() : Config_PerShop
	{
		return $this->config;
	}

	public function getEshop() : EShop
	{
		return $this->eshop;
	}
	

	public function getPricelist(): Pricelist
	{
		return $this->pricelist;
	}
	
	
	public function getAvailability(): Availability
	{
		return $this->availability;
	}
	
	

	protected function getSession() : Session
	{
		if(!$this->session) {
			$this->session = new Session(CashDesk::SESSION_NAME);
		}

		return $this->session;
	}
	

	public function reset(): void
	{
		$this->getSession()->reset();
	}
	
	public function getCart(): ShoppingCart
	{
		return $this->cart;
	}
	
	
}
