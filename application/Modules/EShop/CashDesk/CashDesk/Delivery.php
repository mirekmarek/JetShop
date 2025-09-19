<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;

use JetApplication\Delivery_Method;
use JetApplication\Carrier_DeliveryPoint;

trait CashDesk_Delivery {
	
	/**
	 * @var Delivery_Method[]
	 */
	protected ?array $available_delivery_methods = null;
	

	/**
	 * @return Delivery_Method[]
	 */
	public function getAvailableDeliveryMethods() : array
	{

		if($this->available_delivery_methods===null) {
			$cart = $this->cart;
			$amount = $cart->getAmount();
			
			$this->available_delivery_methods = Delivery_Method::getAvailableByProducts(
				$cart->getEshop(),
				$cart->getProducts()
			);
			
			foreach($this->available_delivery_methods as $i=>$method) {
				if(
					(
						$method->getMinimalOrderAmount() &&
						$amount<$method->getMinimalOrderAmount()
					) ||
					(
						$method->getMaximalOrderAmount() &&
						$amount>$method->getMinimalOrderAmount()
					)
				) {
					unset($this->available_delivery_methods[$i]);
				}
			}

			$this->sortDeliveryMethods( $this->available_delivery_methods );
		}

		return $this->available_delivery_methods;
	}
	
	public function getDeliveryMethod( int $id ) : ?Delivery_Method
	{
		$methods = $this->getAvailableDeliveryMethods();
		if(!isset($methods[$id])) {
			return null;
		}
		
		return $methods[$id];
	}
	
	public function sortDeliveryMethods( array &$delivery_methods ) : void
	{
		
		uasort( $delivery_methods, function( Delivery_Method $a, Delivery_Method $b ) {
			return $a->getPriority()<=>$b->getPriority();
		} );
		
	}
	

	public function getDefaultDeliveryMethod() : ?Delivery_Method
	{
		$delivery_methods = $this->getAvailableDeliveryMethods();
		
		$methods = [];
		$cheapest = null;
		$pricelist = $this->getPricelist();
		
		foreach($delivery_methods as $delivery_method) {
			if($delivery_method->isPersonalTakeover()) {
				continue;
			}
			
			if($cheapest===null) {
				$cheapest = $delivery_method;
				continue;
			}
			
			if($delivery_method->getPrice($pricelist)<$cheapest->getPrice($pricelist)) {
				$cheapest = $delivery_method;
			}
		}
		
		return $cheapest;
	}

	public function getSelectedDeliveryMethod() : ?Delivery_Method
	{
		$session = $this->getSession();

		$methods = $this->getAvailableDeliveryMethods();

		$default_method = $this->getDefaultDeliveryMethod();
		if(!$default_method) {
			return null;
		}

		$id = $session->getValue('selected_delivery_method', $default_method->getId());

		if(!isset($methods[$id])) {
			$session->setValue('selected_delivery_method', $default_method->getId());
			$session->setValue('selected_delivery_method_point_code', '');

			return $default_method;
		}

		$method = $methods[$id];

		if(
			$method->isPersonalTakeover() &&
			!$method->getPersonalTakeoverDeliveryPoint(
				$session->getValue('selected_personal_takeover_point_code', '')
			)
		) {
			$session->setValue('selected_delivery_method', $default_method->getId());
			$session->setValue('selected_delivery_method_point_code', '');

			return $default_method;
		}

		return $methods[$id];
	}

	public function selectDeliveryMethod( int $id ) : bool
	{
		$session = $this->getSession();

		$methods = $this->getAvailableDeliveryMethods();

		if(!isset( $methods[$id])) {
			return false;
		}
		if($session->getValue('selected_delivery_method')!=$id) {
			$session->setValue('selected_delivery_method', $id);
			$session->setValue('selected_personal_takeover_point_code', '');
		}

		return true;
	}

	public function selectPersonalTakeoverDeliveryPoint( Delivery_Method $method, Carrier_DeliveryPoint $point ) : bool
	{
		$point = $method->getPersonalTakeoverDeliveryPoint( $point->getPointCode() );

		if( !$point ) {
			return false;
		}

		$session = $this->getSession();
		$session->setValue('selected_delivery_method', $method->getId() );
		$session->setValue('selected_personal_takeover_point_code', $point->getPointCode() );

		return true;
	}

	public function getSelectedPersonalTakeoverDeliveryPoint() : ?Carrier_DeliveryPoint
	{
		$session = $this->getSession();
		$method = $this->getSelectedDeliveryMethod();

		$place_code = $session->getValue('selected_personal_takeover_point_code');

		if(!$place_code) {
			return null;
		}

		return $method->getPersonalTakeoverDeliveryPoint( $place_code );
	}
	
	public function getDeliveryPointByCode( string $code, ?Delivery_Method &$method=null ) : ?Carrier_DeliveryPoint
	{
		if(
			!$code ||
			!str_contains($code, ':')
		) {
			return null;
		}
		
		[$method_id, $point_code] = explode(':', $code);
		
		$method = Delivery_Method::get( (int)$method_id );
		if(!$method || !$method->isActive()) {
			return null;
		}
		
		$point = Carrier_DeliveryPoint::getPoint(
			$method->getCarrier(),
			$point_code
		);
		
		if(!$point) {
			return null;
		}
		
		return $point;
	}
}