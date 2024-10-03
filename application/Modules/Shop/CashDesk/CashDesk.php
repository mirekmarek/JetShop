<?php
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Session;

use JetApplication\Availabilities_Availability;
use JetApplication\Pricelists_Pricelist;
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
	
	
	protected Config_PerShop $config;
	protected Shops_Shop $shop;
	protected Pricelists_Pricelist $pricelist;
	protected Availabilities_Availability $availability;

	protected ?Session $session = null;

	protected bool $billing_address_editable = false;

	protected bool $delivery_address_editable = false;
	

	public function __construct(
		Config_PerShop $config,
		Shops_Shop $shop,
		Pricelists_Pricelist $pricelist,
		Availabilities_Availability $availability
	) {
		$this->config = $config;
		$this->shop = $shop;
		$this->pricelist = $pricelist;
		$this->availability = $availability;
	}
	
	public function getConfig() : Config_PerShop
	{
		return $this->config;
	}

	public function getShop() : Shops_Shop
	{
		return $this->shop;
	}
	

	public function getPricelist(): Pricelists_Pricelist
	{
		return $this->pricelist;
	}
	
	
	public function getAvailability(): Availabilities_Availability
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
}
