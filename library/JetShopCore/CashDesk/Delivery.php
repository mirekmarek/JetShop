<?php
namespace JetShop;

use Jet\Session;

trait Core_CashDesk_Delivery {

	protected ?array $available_delivery_methods = null;

	/**
	 * @return Delivery_Method[]
	 */
	public function getAvailableDeliveryMethods() : iterable
	{
		if($this->available_delivery_methods===null) {
			$this->available_delivery_methods = [];

			$cart = ShoppingCart::get();

			$delivery_classes = [];
			$delivery_kinds = [];

			foreach($cart->getItems() as $item) {
				$product = $item->getProduct();

				$delivery_class = $product->getDeliveryClass();
				if(!$delivery_class) {
					continue;
				}

				if(!isset($delivery_classes[$delivery_class->getCode()])) {
					$delivery_classes[$delivery_class->getCode()] = $delivery_class;

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
				foreach($class->getDeliveryMethods() as $method) {
					if(!$method->getShopData($this->shop_code)->isActive()) {
						continue;
					}

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

					$this->available_delivery_methods[$method->getCode()] = $method;

				}
			}

			$this->getModule()->sortDeliveryMethods( $this, $this->available_delivery_methods );

		}

		return $this->available_delivery_methods;
	}

	public function getDefaultDeliveryMethod() : Delivery_Method
	{
		/**
		 * @var CashDesk_Module $module
		 * @var CashDesk $this
		 */
		$methods = $this->getAvailableDeliveryMethods();

		$module = $this->getModule();

		return $module->getDefaultDeliveryMethod( $this, $methods );
	}

	public function getSelectedDeliveryMethod() : Delivery_Method
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$methods = $this->getAvailableDeliveryMethods();

		$default_method = $this->getDefaultDeliveryMethod();

		$code = $session->getValue('selected_delivery_method', $default_method->getCode());

		if(!isset($methods[$code])) {
			$session->setValue('selected_delivery_method', $default_method->getCode());
			$session->setValue('selected_delivery_method_place_code', '');

			return $default_method;
		}

		return $methods[$code];
	}

	public function selectDeliveryMethod( string $code ) : bool
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$methods = $this->getAvailableDeliveryMethods();

		if(!isset($methods[$code])) {
			return false;
		}
		if($session->getValue('selected_delivery_method')!=$code) {
			$session->setValue('selected_delivery_method', $code);
			$session->setValue('selected_personal_takeover_place_code', '');
		}

		return true;
	}

	public function selectPersonalTakeoverPlace( string $method_code, string $place_code ) : bool
	{

		$place = Delivery_PersonalTakeover_Place::getPlace( $this->shop_code, $method_code, $place_code );

		if(
			!$place ||
			!$place->isActive()
		) {
			return false;
		}

		if(!$this->selectDeliveryMethod($method_code)) {
			return false;
		}

		/**
		 * @var Session $session
		 */
		$session = $this->getSession();
		$method = $this->getSelectedDeliveryMethod();

		if(
			!$method->hasPersonalTakeoverPlace( $this->shop_code, $place_code )
		) {
			$place_code = '';
		}

		$session->setValue('selected_personal_takeover_place_code', $place_code);

		return true;
	}

	public function getSelectedPersonalTakeoverPlace() : ?Delivery_PersonalTakeover_Place
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();
		$method = $this->getSelectedDeliveryMethod();

		$place_code = $session->getValue('selected_personal_takeover_place_code');

		if(!$place_code) {
			return null;
		}

		return $method->getPersonalTakeoverPlace( $this->shop_code, $place_code );
	}
}