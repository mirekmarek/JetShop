<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Discounts\Code;

use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Input;
use Jet\MVC_Page_Content;
use Jet\Session;
use Jet\Tr;

use JetApplication\CashDesk;
use JetApplication\Discounts_Code;
use JetApplication\Discounts_Discount;
use JetApplication\Discounts_Module;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Round;
use JetApplication\Shop_Managers;

/**
 *
 */
class Main extends Discounts_Module
{
	public const DISCOUNT_MODULE = 'Code';

	protected ?Session $session = null;

	protected ?Form $used_form = null;

	protected function getSession() : Session
	{
		if(!$this->session) {
			$this->session = new Session('discount_code');
		}

		return $this->session;
	}
	
	/**
	 * @return Discounts_Code[]
	 */
	public function getUsedCodes() : array
	{
		$session = $this->getSession();
		$code_ids = $session->getValue('code_ids', []);

		if(!$code_ids) {
			return [];
		}
		
		$valid_code_ids = [];
		$codes = [];
		foreach($code_ids as $id) {
			$code = Discounts_Code::get($id);
			if(
				!$code ||
				!$code->isValid()
			) {
				continue;
			}

			$valid_code_ids[] = $code->getId();
			$codes[$code->getId()] = $code;
		}
		
		$session->setValue('code_ids', $valid_code_ids);
		
		return $codes;
	}

	public function useCode( Discounts_Code $code ) : void
	{
		$used_codes = $this->getUsedCodes();
		if(isset($used_codes[$code->getId()])) {
			return;
		}
		
		$ids = array_keys($used_codes);
		$ids[] = $code->getId();
		
		$this->getSession()->setValue('code_ids', $ids);
	}

	public function cancelUse( Discounts_Code $code ) : void
	{
		$used_codes = $this->getUsedCodes();
		if(!isset($used_codes[$code->getId()])) {
			return;
		}
		unset($used_codes[$code->getId()]);
		
		
		$ids = array_keys($used_codes);
		$this->getSession()->setValue('code_ids', $ids);
	}


	public function getUseCodeForm() : Form
	{
		if(!$this->used_form) {
			$code_input = new Form_Field_Input('code', '');
			$code_input->setIsRequired( true );
			$code_input->setPlaceholder(Tr::_('Discount code'));

			$code_input->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter your discount code'
			]);

			$this->used_form = new Form( 'discount_code_use_form', [$code_input] );
			$this->used_form->setAction('?action=use_code');
			
			$code_input->setErrorMessages([
				'unknown_code' => 'Unknown discount code',
				'future_code' => 'The discount code will not be active until the future',
				'past_code' => 'The discount code is no longer valid',
				'used' => 'The discount code has been already used',
				'under_min_value' => 'The minimum order value must be at least %MIN%',
			]);

			$code_input->setValidator(function( Form_Field_Input $code_input ) {

				$code = Discounts_Code::getByCode( $code_input->getValue() );

				if(!$code) {
					$code_input->setError('unknown_code');
					return false;
				}

				if( !$code->isValid($error_code, $error_data) ) {
					$code_input->setError($error_code, $error_data);
					return false;
				}

				$this->useCode( $code );

				return true;
			});

		}

		return $this->used_form;
	}

	public function ShoppingCart_handle() : string
	{
		$content = new class extends MVC_Page_Content {
			public function output( string $output ): void
			{
				$this->output = $output;
			}
		};

		$content->setModuleName( $this->getModuleManifest()->getName() );


		$controller = new Controller_ShoppingCart( $content );

		$content->setControllerAction( $controller->resolve() );
		$controller->dispatch();

		return $content->getOutput();

	}


	public function generateDiscounts( CashDesk $cash_desk ) : void
	{
		$used_codes = $this->getUsedCodes();

		if(!$used_codes) {
			return;
		}
		
		$discounts = [];
		foreach($used_codes as $used_code) {
			$discount = new Discounts_Discount();
			$discount->setDiscountModule( static::DISCOUNT_MODULE );
			$discount->setDiscountContext( $used_code->getCode() );
			
			switch($used_code->getDiscountType()) {
				
				case Discounts_Discount::DISCOUNT_TYPE_PRODUCTS_PERCENTAGE:
					
					$discount->setDescription( Tr::_('Discount %D%% for products price', [
						'D'=>$cash_desk->getShop()->getLocale()->formatFloat($used_code->getDiscount())
					]) );
					
					
					$discount->setVatRate( $cash_desk->getShop()->getDefaultVatRate() );
					
					$discount->setAmount(
						Round::round(
							$cash_desk->getShop(),
							Shop_Managers::ShoppingCart()->getCart()->getAmount()
							*
							$used_code->getDiscountPercentageMtp()
						)
					);
					
					

					break;
					
				case Discounts_Discount::DISCOUNT_TYPE_PRODUCTS_AMOUNT:
					
					$discount->setDescription( Tr::_('Discount %D% for products price', [
						'D'=>Shop_Managers::PriceFormatter()->formatWithCurrency( $used_code->getDiscount(), $cash_desk->getShop() )
					]) );
					
					$discount->setVatRate( $cash_desk->getShop()->getDefaultVatRate() );
					
					$discount->setAmount(
						$used_code->getDiscount()
					);
					
					
					break;
				
				case Discounts_Discount::DISCOUNT_TYPE_DELIVERY_PERCENTAGE:
					$discount->setDescription( Tr::_('Discount %D%% for delivery price', [
						'D'=>$cash_desk->getShop()->getLocale()->formatFloat($used_code->getDiscount())
					]) );
					
					foreach($cash_desk->getAvailableDeliveryMethods() as $method) {
						if($method->getDiscountIsNotAllowed()) {
							continue;
						}
						
						$method->setPrice(
							Round::round(
								$cash_desk->getShop(),
								$method->getDefaultPrice()
								*
								$used_code->getDiscountPercentageMtp()
							)
						);
					}
					
					break;
					
				case Discounts_Discount::DISCOUNT_TYPE_DELIVERY_AMOUNT:
					$discount->setDescription( Tr::_('Discount %D% for delivery price', [
						'D'=>Shop_Managers::PriceFormatter()->formatWithCurrency( $used_code->getDiscount(), $cash_desk->getShop() )
					]) );
					
					foreach($cash_desk->getAvailableDeliveryMethods() as $method) {
						if($method->getDiscountIsNotAllowed()) {
							continue;
						}
						
						$price = $method->getDefaultPrice()-$used_code->getDiscount();
						if($price<0) {
							$price = 0;
						}
						
						$method->setPrice( $price );
					}
					
					break;
				
			}
			
			$cash_desk->addDiscount( $discount );
			
		}
		
	}
	
	public function checkDiscounts( CashDesk $cash_desk ) : void
	{
	}
	
	
	
	
	public function Order_saved( Order $order ) : void
	{
		$codes = $this->getUsedCodes();
		
		foreach($codes as $code) {
			$code->used( $order );
		}
		
		$this->getSession()->setValue('code_ids', []);
	}

	public function Order_canceled( Order $order ) : void
	{
		Discounts_Code::cancelUsages( $order );
	}

	
	
	protected function _getCodeByOrderItem( Order_Item $item ) : ?Discounts_Code
	{
		if(
			$item->getType()!=Order_Item::ITEM_TYPE_DISCOUNT ||
			$item->getItemCode()!=static::DISCOUNT_MODULE
		) {
			return null;
		}

		[$code_id] = explode(':', $item->getSubCode());

		return Discounts_Code::get($code_id);
	}

	public function Order_itemRemoved( Order $order, Order_Item $item ) : void
	{
		if(($code=$this->_getCodeByOrderItem($item))) {
			$code->cancelUsage( $order );
		}
	}

	public function Order_itemAdded( Order $order, Order_Item $item ) : void
	{
		if(($code=$this->_getCodeByOrderItem($item))) {
			$code->used($order);
		}
	}

	public function Order_reactivated( Order $order, Order_Item $item ) : void
	{
		if(($code=$this->_getCodeByOrderItem($item))) {
			$code->used($order);
		}
	}


	public function CashDesk_RegisteredCustomer_handle(): string
	{
		return '';
	}

}