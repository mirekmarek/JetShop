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
use JetApplication\EShop_Managers;

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
		$code_ids = $this->getUsedCodesRaw();

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
		
		$this->setUsedCodesRaw( $valid_code_ids );
		
		return $codes;
	}
	
	public function getUsedCodesRaw() : array
	{
		$session = $this->getSession();
		$code_ids = $session->getValue('code_ids', []);
		
		if(!$code_ids) {
			return [];
		}
		
		return $code_ids;
	}
	
	protected function setUsedCodesRaw( array $code_ids ): void
	{
		$session = $this->getSession();
		$this->getSession()->setValue('code_ids', $code_ids);
	}
	

	public function useCode( Discounts_Code $code ) : void
	{
		$used_codes = $this->getUsedCodes();
		if(isset($used_codes[$code->getId()])) {
			return;
		}
		
		$ids = array_keys($used_codes);
		$ids[] = $code->getId();
		
		$this->setUsedCodesRaw( $ids );
		
		$this->used_form = null;
		EShop_Managers::CashDesk()->getCashDesk()->getDiscounts(true);
		
	}

	public function cancelUse( Discounts_Code $code ) : void
	{
		$used_codes = $this->getUsedCodes();
		
		if(!isset($used_codes[$code->getId()])) {
			return;
		}
		unset($used_codes[$code->getId()]);
		
		
		$ids = array_keys($used_codes);
		$this->setUsedCodesRaw( $ids );
		EShop_Managers::CashDesk()->getCashDesk()->getDiscounts(true);
		
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
				'no_allowed_products_in_cart' => 'The discount code is only for certain products. Unfortunately you do not have such products in your cart.',
				'do_not_combine' => 'Discount code cannot be combined with other codes.',
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
					
					if($used_code->getRelevanceMode()!=Discounts_Code::RELEVANCE_MODE_ALL) {
						$discount->setDescription( Tr::_('Discount %D%% for some of products price', [
							'D'=>$cash_desk->getEshop()->getLocale()->formatFloat($used_code->getDiscount())
						]) );
					} else {
						$discount->setDescription( Tr::_('Discount %D%% for products price', [
							'D'=>$cash_desk->getEshop()->getLocale()->formatFloat($used_code->getDiscount())
						]) );
					}
					
					
					$discount->setVatRate( $cash_desk->getPricelist()->getDefaultVatRate() );
					
					$discount->setAmount(
						$cash_desk->getPricelist()->round(
							$used_code->getRelevantProductAmount()
								*
									$used_code->getDiscountPercentageMtp()
						)
					);
					
					

					break;
					
				case Discounts_Discount::DISCOUNT_TYPE_PRODUCTS_AMOUNT:
					
					$discount->setDescription( Tr::_('Discount %D% for products price', [
						'D'=>EShop_Managers::PriceFormatter()->formatWithCurrency(
							$used_code->getDiscount(),
							$cash_desk->getPricelist()
						)
					]) );
					
					$discount->setVatRate( $cash_desk->getPricelist()->getDefaultVatRate() );
					
					$discount->setAmount(
						$used_code->getDiscount()
					);
					
					
					break;
				
				case Discounts_Discount::DISCOUNT_TYPE_DELIVERY_PERCENTAGE:
					$discount->setVatRate( $cash_desk->getSelectedDeliveryMethod()->getVatRate( $cash_desk->getPricelist() ) );
					$discount->setAmount( 0 );
					
					
					$discount->setDescription( Tr::_('Discount %D%% for delivery price', [
						'D'=>$cash_desk->getEshop()->getLocale()->formatFloat($used_code->getDiscount())
					]) );
					
					foreach($cash_desk->getAvailableDeliveryMethods() as $method) {
						if($method->getDiscountIsNotAllowed()) {
							continue;
						}
						
						$default_price = $method->getDefaultPrice( $cash_desk->getPricelist() );
						
						$discount_value = $cash_desk->getPricelist()->round( $default_price * $used_code->getDiscountPercentageMtp() );
						
						
						$method->setPrice(
							$cash_desk->getPricelist(),
							$default_price + $discount_value
						);
					}
					
					break;
					
				case Discounts_Discount::DISCOUNT_TYPE_DELIVERY_AMOUNT:
					$discount->setVatRate( $cash_desk->getSelectedDeliveryMethod()->getVatRate( $cash_desk->getPricelist() ) );
					$discount->setAmount( 0 );
					
					$discount->setDescription( Tr::_('Discount %D% for delivery price', [
						'D'=>EShop_Managers::PriceFormatter()->formatWithCurrency(
							$used_code->getDiscount(),
							$cash_desk->getPricelist()
						)
					]) );
					
					foreach($cash_desk->getAvailableDeliveryMethods() as $method) {
						if($method->getDiscountIsNotAllowed()) {
							continue;
						}
						
						$price = $method->getDefaultPrice( $cash_desk->getPricelist() )-$used_code->getDiscount();
						if($price<0) {
							$price = 0;
						}
						
						$method->setPrice(
							$cash_desk->getPricelist(),
							$price
						);
					}
					
					break;
				
			}
			
			$cash_desk->addDiscount( $discount );
			
		}
		
	}
	
	public function checkDiscounts( CashDesk $cash_desk ) : void
	{
	}
	
	
	
	
	public function Order_newOrderCreated( Order $order ) : void
	{
		$codes = $this->getUsedCodes();
		
		foreach($codes as $code) {
			$code->used( $order );
		}
		
		$this->setUsedCodesRaw([]);
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
	
	public function isPossibleToAddCode() : bool
	{
		foreach($this->getUsedCodes() as $coupon) {
			if($coupon->getDoNotCombine()) {
				return false;
			}
		}
		
		return true;
	}
	

}