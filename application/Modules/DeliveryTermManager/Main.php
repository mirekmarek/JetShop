<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\DeliveryTermManager;

use Jet\Data_DateTime;
use JetApplication\Application_Service_General;
use JetApplication\Availabilities;
use JetApplication\DeliveryTerm;
use JetApplication\DeliveryTerm_Info;
use JetApplication\Application_Service_General_DeliveryTerm;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\Availability;


class Main extends Application_Service_General_DeliveryTerm
{
	public function getInfo( Product_EShopData $product, float $units_required=1, ?Availability $availability=null, ?Data_DateTime $date_time=null ) : DeliveryTerm_Info
	{
		if(!$availability) {
			$availability = Availabilities::getCurrent();
		}
		
		$info = new DeliveryTerm_Info();
		$info->setAvailability( $availability );
		$info->setEshop( $product->getEshop() );
		$info->setNumberofUnitsRequired( $units_required );
		
		if($product->isVirtual()) {
			$info->setIsVirtualProduct( true );
			$info->setLengthOfDelivery( 0 );
			$info->setDeliveryInfoText( '' );
			$info->setDeliveryInfoTextWhenAvailable( '' );
			$info->setDeliveryInfoTextWhenNotAvailable( '' );
			$info->setNumberOfUnitsAvailable( 9999999 );
			$info->setSituation( DeliveryTerm::SITUATION_IN_STOCK );
			
			return $info;
		}
		
		$info->setNumberOfUnitsAvailable( $product->getNumberOfAvailable( $availability ) );
		
		if(!$product->getLengthOfDelivery( $availability )) {
			$info->setLengthOfDeliveryWhenNotAvailable( 5 );
		} else {
			$info->setLengthOfDeliveryWhenNotAvailable( $product->getLengthOfDelivery( $availability ) );
		}
		
		$dispatch_info = Application_Service_General::Calendar()->getNumberOfDaysRequiredForDispatch( $product->getEshop(), $date_time );
		$info->setLengthOfDeliveryWhenAvailable( $dispatch_info->getNumberOfDays() );
		
		if(!$product->getAllowToOrderWhenSoldOut()) {
			$info->setAllowToOrderMore( false );
		}
		
		
		
		$info->setDeliveryInfoTextWhenAvailable( 'In stock' );

		
		if(!$product->getAllowToOrderWhenSoldOut()) {
			$info->setDeliveryInfoTextWhenNotAvailable( 'Not available' );
		} else {
			
			$available_from = $product->getAvailableFrom( $availability );
			if($available_from) {
				$info->setDeliveryInfoTextWhenNotAvailable( 'Available from %date%' );
				$info->setAvailableFromDate( $available_from );
			} else {
				
				$days_map = [
					1 => 'One working day',
					2 => 'Two working days',
					3 => 'Three working days',
					4 => 'Four working days',
					'default' => 'One week'
				];
				
				$weeks_map = [
					1 => 'One week',
					2 => 'Two weeks',
					3 => 'Three weeks',
					4 => 'Four weeks',
					'default' => 'More than four weeks'
				];
				
				if($info->getLengthOfDeliveryWhenNotAvailable()<=5) {
					$info->setDeliveryInfoTextWhenNotAvailable( $days_map[$info->getLengthOfDelivery()]??$days_map['default'] );
				} else {
					$weeks = ceil($info->getLengthOfDeliveryWhenNotAvailable() / 5);
					
					$info->setDeliveryInfoTextWhenNotAvailable( $weeks_map[$weeks]??$weeks_map['default'] );
				}

			}
		}
		
		
		
		$info->setSituationWhenAvailable( DeliveryTerm::SITUATION_IN_STOCK );
		
		
		if(!$product->getAllowToOrderWhenSoldOut()) {
			$info->setSituationWhenNotAvailable( DeliveryTerm::SITUATION_NOT_AVAILABLE );
		} else {
			$available_from = $product->getAvailableFrom( $availability );
			if($available_from) {
				$info->setSituationWhenNotAvailable( DeliveryTerm::SITUATION_GOOD );
			} else {
				
				$info->setSituationWhenNotAvailable( DeliveryTerm::SITUATION_TERRIBLE );
				
				if($info->getLengthOfDelivery()<=15) {
					$info->setSituationWhenNotAvailable( DeliveryTerm::SITUATION_BAD );
				}
				if($info->getLengthOfDelivery()<=10) {
					$info->setSituationWhenNotAvailable( DeliveryTerm::SITUATION_SO_SO );
				}
				
				if($info->getLengthOfDelivery()<=5) {
					$info->setSituationWhenNotAvailable( DeliveryTerm::SITUATION_GOOD );
				}
			}
		}
		
		
		if($info->getNumberOfUnitsAvailable()>=$info->getNumberofUnitsRequired()) {
			$info->setSituation( $info->getSituationWhenAvailable() );
			$info->setDeliveryInfoText( $info->getDeliveryInfoTextWhenAvailable() );
			$info->setLengthOfDelivery( $info->getLengthOfDeliveryWhenAvailable() );
		} else {
			$info->setSituation( $info->getSituationWhenNotAvailable() );
			$info->setDeliveryInfoText( $info->getDeliveryInfoTextWhenNotAvailable() );
			$info->setLengthOfDelivery( $info->getLengthOfDeliveryWhenNotAvailable() );
		}
		
		
		return $info;
	}
	
	public function setupOrder( Order $order ) : void
	{
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
					
					$delivery_info = $this->getInfo( $product, $item->getNumberOfUnits(), $order->getAvailability(), $order->getDatePurchased() );
					
					$set_item->setupDeliveryTermInfo( $order, $delivery_info );
					
				}
			}

			
			$product = Product_EShopData::get( $item->getItemId(), $order->getEshop() );
			if(!$product) {
				continue;
			}
			
			$delivery_info = $this->getInfo( $product, $item->getNumberOfUnits(), $order->getAvailability(), $order->getDatePurchased() );

			$item->setupDeliveryTermInfo( $order, $delivery_info );
		}
		$order->save();
		
		
		$order_promised_delivery_date = Data_DateTime::now();
		foreach($order->getItems() as $item) {
			if(!$item->isPhysicalProduct()) {
				continue;
			}
			
			if($item->getSetItems()) {
				foreach($item->getSetItems() as $set_item) {
					$pd = $set_item->getPromisedDeliveryDate();
					if(
						$pd &&
						$pd>$order_promised_delivery_date
					) {
						$order_promised_delivery_date = $pd;
					}
				}
				continue;
			}
			
			$pd = $item->getPromisedDeliveryDate();
			
			if(
				$pd &&
				$pd>$order_promised_delivery_date
			) {
				$order_promised_delivery_date = $pd;
			}
		}
		
		$order->setPromisedDeliveryDate( $order_promised_delivery_date );
		
		$order->save();
	}
}