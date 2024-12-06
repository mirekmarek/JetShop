<?php
namespace JetApplicationModule\EShop\CashDesk;

use Jet\Session;

use JetApplication\Availability;
use JetApplication\Pricelist;
use JetApplication\EShop;
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
	protected EShop $eshop;
	protected Pricelist $pricelist;
	protected Availability $availability;

	protected ?Session $session = null;

	protected bool $billing_address_editable = false;

	protected bool $delivery_address_editable = false;
	

	public function __construct(
		Config_PerShop              $config,
		EShop                       $eshop,
		Pricelist                   $pricelist,
		Availability $availability
	) {
		$this->config = $config;
		$this->eshop = $eshop;
		$this->pricelist = $pricelist;
		$this->availability = $availability;
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
}
