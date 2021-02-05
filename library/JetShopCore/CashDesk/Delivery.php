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

					$kind = $delivery_class->getKind();

					if(!in_array($kind, $delivery_kinds)) {
						$delivery_kinds[] = $kind;
					}
				}
			}

			if(in_array(Delivery_Kind::KIND_PERSONAL_TAKEOVER, $delivery_kinds)) {
				//There is something what is available only as "personal take over item" in the order. So only personal takeover methods are allowed

				foreach( $delivery_classes as $code=>$class ) {
					if($class->getKind()!=Delivery_Kind::KIND_PERSONAL_TAKEOVER) {
						unset($delivery_classes[$code]);
					}
				}
			}

			if(
				in_array(Delivery_Kind::KIND_E_DELIVERY, $delivery_kinds) &&
				(
					in_array(Delivery_Kind::KIND_PERSONAL_TAKEOVER, $delivery_kinds) ||
					in_array(Delivery_Kind::KIND_DELIVERY, $delivery_kinds)
				)
			) {
				//There is some physical item in the order. So e-delivery is not allowed for such order

				foreach( $delivery_classes as $code=>$class ) {
					if($class->getKind()==Delivery_Kind::KIND_E_DELIVERY) {
						unset($delivery_classes[$code]);
					}
				}
			}


			$avl_methods = Delivery_Method::getList();

			$delivery_class_codes = array_keys($delivery_classes);

			foreach($avl_methods as $method) {
				if(
					$method->getShopData($this->shop_id)->isActive() &&
					array_intersect( $delivery_class_codes, $method->getDeliveryClassCodes() )
				) {
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

	public function selectDeliveryMethod( $code ) : void
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();

		$methods = $this->getAvailableDeliveryMethods();

		if(!isset($methods[$code])) {
			return;
		}

		if($session->getValue('selected_delivery_method')!=$code) {
			$session->setValue('selected_delivery_method', $code);
			$session->setValue('selected_personal_takeover_place_code', '');
		}
	}

	public function selectPersonalTakeOverPlace( string $place_code ) : void
	{
		/**
		 * @var Session $session
		 */
		$session = $this->getSession();
		$method = $this->getSelectedDeliveryMethod();

		if(
			!$method->hasPersonalTakeOverPlace( $this->shop_id, $place_code )
		) {
			$place_code = '';
		}

		$session->setValue('selected_personal_takeover_place_code', $place_code);
	}

	public function getSelectedPersonalTakeOverPlace() : ?Delivery_PersonalTakeover_Place
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

		return $method->getPersonalTakeOverPlace( $this->shop_id, $place_code );
	}
}