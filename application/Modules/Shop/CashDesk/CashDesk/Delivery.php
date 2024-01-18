<?php
namespace JetApplicationModule\Shop\CashDesk;

use JetApplication\Delivery_Class;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Shop_Managers;
use JetApplication\Delivery_Kind;
use JetApplication\Delivery_PersonalTakeover_Place;

trait CashDesk_Delivery {
	
	/**
	 * @var Delivery_Method_ShopData[]
	 */
	protected ?array $available_delivery_methods = null;
	

	/**
	 * @return Delivery_Method_ShopData[]
	 */
	public function getAvailableDeliveryMethods() : array
	{

		if($this->available_delivery_methods===null) {
			$this->available_delivery_methods = [];

			$default_delivery_class = Delivery_Class::getDefault();
			$cart = Shop_Managers::ShoppingCart()->getCart();

			$delivery_classes = [];
			$delivery_kinds = [];

			foreach($cart->getItems() as $item) {
				$product = $item->getProduct();

				$delivery_class_id = $product->getDeliveryClassId();
				if(!$delivery_class_id) {
					if(!$default_delivery_class) {
						continue;
					}
					
					$delivery_class_id = $default_delivery_class->getId();
				}

				if(!isset($delivery_classes[$delivery_class_id])) {
					$delivery_class = Delivery_Class::load( $delivery_class_id );
					
					$delivery_classes[$delivery_class_id] = $delivery_class;

					foreach($delivery_class->getKinds() as $kind ) {
						$kind = $kind->getCode();

						if(!in_array($kind, $delivery_kinds)) {
							$delivery_kinds[] = $kind;
						}

					}
				}
			}

			$has_only_personal_takeover = false;
			$has_only_e_delivery = null;


			foreach( $delivery_classes as $delivery_class ) {
				if($delivery_class->isPersonalTakeOverOnly()) {
					$has_only_personal_takeover = true;
				}

				if($delivery_class->isEDelivery()) {
					if($has_only_e_delivery===null) {
						$has_only_e_delivery = true;
					}
				} else {
					$has_only_e_delivery = false;
				}
			}

			foreach($delivery_classes as $class ) {
				$methods = Delivery_Method_ShopData::getActiveList( $class->getDeliveryMethodIds() );
				
				foreach( $methods as $method ) {
					if(
						$has_only_personal_takeover &&
						$method->getKindCode()!=Delivery_Kind::KIND_PERSONAL_TAKEOVER
					) {
						//There is something what is available only as "personal take over item" in the order. So only personal takeover methods are allowed
						continue;
					}

					if(
						$has_only_e_delivery &&
						$method->getKindCode()!=Delivery_Kind::KIND_E_DELIVERY
					) {
						//There is something virtual and nothing else. So only e-delivery is allowed
						continue;
					}

					if(
						!$has_only_e_delivery &&
						$method->getKindCode()==Delivery_Kind::KIND_E_DELIVERY
					) {
						//There is something physical. So e-delivery is not allowed
						continue;
					}

					$this->available_delivery_methods[$method->getId()] = $method;

				}
			}

			foreach($this->available_delivery_methods as $code=>$method) {
				$method->getBackendModule()?->init( $method );
			}

			$this->sortDeliveryMethods( $this->available_delivery_methods );

		}

		return $this->available_delivery_methods;
	}
	
	public function getDeliveryMethod( int $id ) : ?Delivery_Method_ShopData
	{
		$methods = $this->getAvailableDeliveryMethods();
		if(!isset($methods[$id])) {
			return null;
		}
		
		return $methods[$id];
	}
	
	public function sortDeliveryMethods( array &$delivery_methods ) : void
	{
		
		uasort( $delivery_methods, function( Delivery_Method_ShopData $a, Delivery_Method_ShopData $b ) {
			$p_a = $a->getPriority();
			$p_b = $b->getPriority();
			
			if(!$p_a<$p_b) {
				return -1;
			}
			if(!$p_a>$p_b) {
				return 1;
			}
			return 0;
		} );
		
	}
	

	public function getDefaultDeliveryMethod() : ?Delivery_Method_ShopData
	{
		$delivery_methods = $this->getAvailableDeliveryMethods();
		
		$methods = [];
		$cheapest = null;
		
		foreach($delivery_methods as $delivery_method) {
			if($delivery_method->isPersonalTakeover()) {
				continue;
			}
			
			if($cheapest===null) {
				$cheapest = $delivery_method;
				continue;
			}
			
			if($delivery_method->getPrice()<$cheapest->getPrice()) {
				$cheapest = $delivery_method;
			}
		}
		
		return $cheapest;
	}

	public function getSelectedDeliveryMethod() : ?Delivery_Method_ShopData
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
			$session->setValue('selected_delivery_method_place_code', '');

			return $default_method;
		}

		$method = $methods[$id];

		if(
			$method->isPersonalTakeover() &&
			!$method->getPersonalTakeoverPlace(
				$session->getValue('selected_personal_takeover_place_code', '')
			)
		) {
			$session->setValue('selected_delivery_method', $default_method->getId());
			$session->setValue('selected_delivery_method_place_code', '');

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
			$session->setValue('selected_personal_takeover_place_code', '');
		}

		return true;
	}

	public function selectPersonalTakeoverPlace( int $method_id, string $place_code ) : bool
	{
		if( !($method = CashDesk::get()->getDeliveryMethod( $method_id )) ) {
			return false;
		}
		
		$place = $method->getPersonalTakeoverPlace( $place_code, only_active: true );

		if( !$place ) {
			return false;
		}

		$session = $this->getSession();
		$session->setValue('selected_delivery_method', $method->getId() );
		$session->setValue('selected_personal_takeover_place_code', $place->getPlaceCode() );

		return true;
	}

	public function getSelectedPersonalTakeoverPlace() : ?Delivery_PersonalTakeover_Place
	{
		$session = $this->getSession();
		$method = $this->getSelectedDeliveryMethod();

		$place_code = $session->getValue('selected_personal_takeover_place_code');

		if(!$place_code) {
			return null;
		}

		return $method->getPersonalTakeoverPlace( $place_code );
	}
}