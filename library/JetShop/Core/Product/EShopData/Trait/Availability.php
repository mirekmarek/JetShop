<?php
namespace JetShop;


use Jet\Data_DateTime;
use JetApplication\Availability;
use JetApplication\Product_Availability;

trait Core_Product_EShopData_Trait_Availability
{
	public function getNumberOfAvailable( Availability $availability ) : float
	{
		return Product_Availability::get( $availability, $this->getId() )->getNumberOfAvailable();
	}
	
	public function getLengthOfDelivery( Availability $availability ): int
	{
		return Product_Availability::get( $availability, $this->getId() )->getLengthOfDelivery();
	}
	
	public function getAvailableFrom( Availability $availability ): ?Data_DateTime
	{
		return Product_Availability::get( $availability, $this->getId() )->getAvailableFrom();
	}
	
}