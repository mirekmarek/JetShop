<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\DeliveryTermManager;


use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Availabilities;
use JetApplication\Calendar;
use JetApplication\DeliveryTerm;
use JetApplication\DeliveryTerm_Info;
use JetApplication\DeliveryTerm_Manager;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\Availability;


class Main extends Application_Module implements DeliveryTerm_Manager
{
	public function getInfo( Product_EShopData $product, ?Availability $availability=null ) : DeliveryTerm_Info
	{
		if(!$availability) {
			$availability = Availabilities::getCurrent();
		}
		
		$info = new DeliveryTerm_Info();
		$info->setAvailability( $availability );
		$info->setEshop( $product->getEshop() );
		
		if($product->isVirtual()) {
			$info->setIsVirtualProduct( true );
			$info->setLengthOfDelivery( 0 );
			$info->setDeliveryInfoText('');
			$info->setNumberOfUnitsAvailable( 9999999 );
			$info->setSituation( DeliveryTerm::SITUATION_IN_STOCK );
			
			return $info;
		}
		
		$info->setNumberOfUnitsAvailable( $product->getNumberOfAvailable( $availability ) );
		
		if(!$product->getLengthOfDelivery( $availability )) {
			$info->setLengthOfDelivery( 5 );
		} else {
			$info->setLengthOfDelivery( $product->getLengthOfDelivery( $availability ) );
		}
		
		if(!$product->getAllowToOrderWhenSoldOut()) {
			$info->setAllowToOrderMore( false );
		}
		
		
		

		
		if(!$product->getAllowToOrderWhenSoldOut()) {
			$info->setDeliveryInfoText( 'Not available' );
		} else {
			
			$available_from = $product->getAvailableFrom( $availability );
			if($available_from) {
				$info->setDeliveryInfoText( 'Available from %date%' );
				$info->setAvailableFromDate( $available_from );
			} else {
				$days_map = [
					1 => 'One working day',
					2 => 'Two working days',
					3 => 'Three working days',
					4 => 'For working days',
					'default' => 'One week'
				];
				
				$weeks_map = [
					1 => 'One week',
					2 => 'Two weeks',
					3 => 'Three weeks',
					'default' => 'More than three weeks'
				];
				
				if($info->getLengthOfDelivery()<=5) {
					$info->setDeliveryInfoText( $days_map[$info->getLengthOfDelivery()]??$days_map['default'] );
				} else {
					$weeks = ceil($info->getLengthOfDelivery() / 5);
					
					$info->setDeliveryInfoText( $weeks_map[$weeks]??$days_map['default'] );
				}
			}
		}
		
		
		
		
		
		if(!$product->getAllowToOrderWhenSoldOut()) {
			$info->setSituation( DeliveryTerm::SITUATION_NOT_AVAILABLE );
		} else {
			$available_from = $product->getAvailableFrom( $availability );
			if($available_from) {
				$info->setSituation( DeliveryTerm::SITUATION_GOOD );
			} else {
				
				$info->setSituation( DeliveryTerm::SITUATION_TERRIBLE );
				
				if($info->getLengthOfDelivery()<=15) {
					$info->setSituation( DeliveryTerm::SITUATION_BAD );
				}
				if($info->getLengthOfDelivery()<=10) {
					$info->setSituation( DeliveryTerm::SITUATION_SO_SO );
				}
				
				if($info->getLengthOfDelivery()<=5) {
					$info->setSituation( DeliveryTerm::SITUATION_GOOD );
				}
			}
		}
		
		
		return $info;
	}
	
	public function setupOrder( Order $order ) : void
	{
		$lod = $order->getDeliveryMethod()?->getLengthOfDeliveryInWorkingDays()??0;
		
		foreach( $order->getItems() as $item ) {
			if(!$item->isPhysicalProduct()) {
				continue;
			}
			
			if($item->getSetItems()) {
				foreach($item->getSetItems() as $set_item) {
					$product = Product_EShopData::get( $set_item->getItemId(), $order->getEshop() );
					if(!$product) {
						continue;
					}
					
					if($set_item->getNumberOfUnitsAvailable()>0) {
						$set_item->setAvailableUnitsPromisedDeliveryDate( Calendar::getNextBusinessDate(
							eshop: $order->getEshop(),
							number_of_working_days: $lod
						) );
					}
					
					if($set_item->getNumberOfUnitsNotAvailable()>0) {
						$delivery_info = $this->getInfo( $product, $order->getAvailability() );
						$set_item->setNotAvailableUnitsDeliveryTemInfo( $delivery_info );
						$set_item->setNotAvailableUnitsPromisedDeliveryDate( $delivery_info->getEstimatedArrivalDateByDeliveryMethod( $lod ) );
					}
					
				}
			}

			
			$product = Product_EShopData::get( $item->getItemId(), $order->getEshop() );
			if(!$product) {
				continue;
			}
			
			if($item->getNumberOfUnitsAvailable()>0) {
				$item->setAvailableUnitsPromisedDeliveryDate( Calendar::getNextBusinessDate(
					eshop: $order->getEshop(),
					number_of_working_days: $lod
				) );
			}
			
			if($item->getNumberOfUnitsNotAvailable()>0) {
				$delivery_info = $this->getInfo( $product, $order->getAvailability() );
				$item->setNotAvailableUnitsDeliveryTemInfo( $delivery_info );
				$item->setNotAvailableUnitsPromisedDeliveryDate( $delivery_info->getEstimatedArrivalDateByDeliveryMethod( $lod ) );
			}
		}
		
		$order_promised_delivery_date = Data_DateTime::now();
		foreach($order->getItems() as $item) {
			if(!$item->isPhysicalProduct()) {
				continue;
			}
			
			if($item->getSetItems()) {
				foreach($item->getSetItems() as $set_item) {
					if(
						$set_item->getAvailableUnitsPromisedDeliveryDate() &&
						$set_item->getAvailableUnitsPromisedDeliveryDate()>$order_promised_delivery_date
					) {
						$order_promised_delivery_date = $set_item->getAvailableUnitsPromisedDeliveryDate();
					}
					if(
						$set_item->getNotAvailableUnitsPromisedDeliveryDate() &&
						$set_item->getNotAvailableUnitsPromisedDeliveryDate()>$order_promised_delivery_date
					) {
						$order_promised_delivery_date = $set_item->getNotAvailableUnitsPromisedDeliveryDate();
					}
					
				}
				continue;
			}
			
			if(
				$item->getAvailableUnitsPromisedDeliveryDate() &&
				$item->getAvailableUnitsPromisedDeliveryDate()>$order_promised_delivery_date
			) {
				$order_promised_delivery_date = $item->getAvailableUnitsPromisedDeliveryDate();
			}
			if(
				$item->getNotAvailableUnitsPromisedDeliveryDate() &&
				$item->getNotAvailableUnitsPromisedDeliveryDate()>$order_promised_delivery_date
			) {
				$order_promised_delivery_date = $item->getNotAvailableUnitsPromisedDeliveryDate();
			}
		}
		
		$order->setPromisedDeliveryDate( $order_promised_delivery_date );
		
	}
}