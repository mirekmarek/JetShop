<?php
namespace JetApplicationModule\EShop\CashDesk;

use JetApplication\Discounts;
use JetApplication\Discounts_Discount;

trait CashDesk_Discounts
{
	/**
	 * @var Discounts_Discount[]
	 */
	protected ?array $discounts = null;
	
	/**
	 * @return Discounts_Discount[]
	 */
	public function getDiscounts( bool $reset = false ) : array
	{
		if($reset) {
			$this->discounts = null;
		}
		
		if($this->discounts===null) {
			$this->discounts = [];
			
			$d_manager = Discounts::Manager();
			$d_manager->generateDiscounts( $this );
			$d_manager->checkDiscounts( $this );
		}
		
		return $this->discounts;
	}
	
	public function addDiscount( Discounts_Discount $discount ) : void
	{
		foreach($this->discounts as $c_d) {
			if( $c_d->getKey()==$discount->getKey() ) {
				return;
			}
		}
		
		$this->discounts[$discount->getKey()] = $discount;
	}
	
	public function removeDiscount( Discounts_Discount $discount ) : void
	{
		$key = $discount->getKey();
		if(isset($this->discounts[$key])) {
			unset( $this->discounts[$key] );
		}
	}
	
}