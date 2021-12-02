<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\CashDesk;

use Jet\Form;
use Jet\Form_Field_Input;
use JetShop\CashDesk;
use JetShop\CashDesk_Module;
use JetShop\Delivery_Method;
use JetShop\Payment_Method;
use JetShop\Shop_Module_Trait;
use JetShop\Shops_Shop;

/**
 *
 */
class Main extends CashDesk_Module
{
	use Shop_Module_Trait;

	/**
	 *
	 * @return string
	 */
	public function getViewsDir(): string
	{
		return parent::getViewsDir();
		//return $this->_getViewsDir('cash_desk');
	}

	public function sortDeliveryMethods( CashDesk $cash_desk, array &$delivery_methods ) : void
	{
		$shop = CashDesk::get()->getShop();

		uasort( $delivery_methods, function( Delivery_Method $a, Delivery_Method $b ) use ($shop) {
			$p_a = $a->getShopData($shop)->getPriority();
			$p_b = $b->getShopData($shop)->getPriority();

			if(!$p_a<$p_b) {
				return -1;
			}
			if(!$p_a>$p_b) {
				return 1;
			}
			return 0;
		} );

	}

	public function getDefaultDeliveryMethod( CashDesk $cash_desk, array $delivery_methods ) : ?Delivery_Method
	{
		$methods = [];

		/**
		 * @var Delivery_Method[] $delivery_methods
		 */
		foreach($delivery_methods as $delivery_method) {
			if($delivery_method->isPersonalTakeover()) {
				continue;
			}

			$methods[] = $delivery_method;
		}

		//TODO: is default

		uasort($methods, function( Delivery_Method $a, Delivery_Method $b ) use ($cash_desk) {
			$p_a = $cash_desk->getDeliveryPrice( $a )->getFinalPrice();
			$p_b = $cash_desk->getDeliveryPrice( $b )->getFinalPrice();


			if($p_a==$p_b) {
				return 0;
			}

			if($p_a<$p_b) {
				return -1;
			}

			return 1;
		});

		foreach($methods as $method) {
			return $method;
		}

		return null;
	}

	public function sortPaymentMethods( CashDesk $cash_desk, array &$payment_methods ) : void
	{

		$shop = CashDesk::get()->getShop();

		uasort( $payment_methods, function( Payment_Method $a, Payment_Method $b ) use ($shop) {
			$p_a = $a->getShopData($shop)->getPriority();
			$p_b = $b->getShopData($shop)->getPriority();

			if(!$p_a<$p_b) {
				return -1;
			}
			if(!$p_a>$p_b) {
				return 1;
			}
			return 0;
		} );

	}

	public function getDefaultPaymentMethod( CashDesk $cash_desk, array $payment_methods ) : ?Payment_Method
	{
		foreach($payment_methods as $payment_method) {
			return $payment_method;
		}

		return null;
	}

	public function updateBillingAddressForm( CashDesk $cash_desk, Form $form ) : void
	{

		$form->setDefaultLabelWidth([
			Form::LJ_SIZE_MEDIUM => 2,
		]);

		$form->setDefaultFieldWidth([
			Form::LJ_SIZE_MEDIUM => 8,
		]);

		$form->setAction('?action=customer_billing_address_send');

		$form->tag()->addJsAction('onsubmit', "CashDesk.customer.billingAddress.confirm();return false;");

		foreach($form->getFields() as $field) {
			$field->input()->addJsAction('onblur', "CashDesk.customer.billingAddress.sendField(this);");
		}

		$field = $form->getField('phone');
		$field->setViewScript('field/phone');

		$shop = $cash_desk->getShop();

		$field->setValidator( function( Form_Field_Input $field ) use ($shop) {
			return static::phoneValidator( $field, $shop );
		} );

	}

	public static function phoneValidator( Form_Field_Input $field, Shops_Shop $shop ) : bool
	{
		$value_raw = $field->getValueRaw();
		$value_raw = preg_replace('/\D/', '', $value_raw);

		$field->setValue($value_raw);

		$reg_exp = $shop->getPhoneValidationRegExp();

		if(!preg_match($reg_exp, $value_raw)) {
			$field->setError( Form_Field_Input::ERROR_CODE_INVALID_FORMAT );

			return false;
		}

		return true;

	}


	public function updateDeliveryAddressForm( CashDesk $cash_desk, Form $form ) : void
	{
		$form->setDefaultLabelWidth([
			Form::LJ_SIZE_MEDIUM => 2,
		]);

		$form->setDefaultFieldWidth([
			Form::LJ_SIZE_MEDIUM => 8,
		]);

		$form->setAction('?action=customer_delivery_address_send');

		$form->tag()->addJsAction('onsubmit', "CashDesk.customer.deliveryAddress.confirm();return false;");

		foreach($form->getFields() as $field) {
			$field->input()->addJsAction('onblur', "CashDesk.customer.deliveryAddress.sendField(this);");
		}
	}


	public function initAgreeFlags( CashDesk $cash_desk, array &$agree_flags ) : void
	{
	}

}