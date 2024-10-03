<?php
namespace JetShop;


use Jet\Data_DateTime;
use JetApplication\Availabilities_Availability;
use JetApplication\Product_Availability;

trait Core_Product_ShopData_Trait_Availability
{
	public function getNumberOfAvailable( Availabilities_Availability $availability ) : float
	{
		return Product_Availability::get( $availability, $this->getId() )->getNumberOfAvailable();
	}
	
	public function getLengthOfDelivery( Availabilities_Availability $availability ): int
	{
		return Product_Availability::get( $availability, $this->getId() )->getLengthOfDelivery();
	}
	
	public function getAvailableFrom( Availabilities_Availability $availability ): ?Data_DateTime
	{
		return Product_Availability::get( $availability, $this->getId() )->getAvailableFrom();
	}
	
}