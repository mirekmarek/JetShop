<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Order\Discounts\Code;

use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Input;
use Jet\MVC_Page_Content;
use Jet\Session;
use Jet\Tr;

use JetApplication\CashDesk;
use JetApplication\Discounts_Code;
use JetApplication\Discounts_Module;
use JetApplication\Order;
use JetApplication\Order_Item;

/**
 *
 */
class Main extends Discounts_Module
{
	public const ORDER_ITEM_CODE = 'DiscountCode';

	protected ?Session $session = null;

	protected ?Form $used_form = null;

	protected function getSession() : Session
	{
		if(!$this->session) {
			$this->session = new Session('discount_code');
		}

		return $this->session;
	}

	public function getUsedCode() : ?Discounts_Code
	{
		$session = $this->getSession();
		$code_id = (int)$session->getValue('code_id', 0);

		if(!$code_id) {
			return null;
		}

		$code = Discounts_Code::get($code_id);
		if(!$code) {
			$session->reset();
			return null;
		}

		if(!$code->isValid()) {
			return null;
		}

		return $code;
	}

	public function useCode( Discounts_Code $code ) : void
	{
		$this->getSession()->setValue('code_id', $code->getId());
	}

	public function cancelUse() : void
	{
		$this->getSession()->reset();
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
				if(!$code_input->validate_required()) {
					return false;
				}

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

	/**
	 * @param CashDesk $cash_desk
	 *
	 * @return Order_Item[]
	 */
	public function getDiscounts( CashDesk $cash_desk ) : array
	{
		$used_code = $this->getUsedCode();

		if(!$used_code) {
			return [];
		}

		$discount = new Order_Item();
		$discount->setType( Order_Item::ITEM_TYPE_DISCOUNT );
		$discount->setCode( static::ORDER_ITEM_CODE );
		$discount->setSubCode( $used_code->getId().':'.$used_code->getCode() );
		$discount->setQuantity(1);
		$discount->setItemAmount( $used_code->getDiscount() );

		$method = 'setDiscount_'.$used_code->getDiscountType();

		$this->{$method}($discount, $used_code);

		return [$discount];
	}

	protected function setDiscount_delivery_amount( Order_Item $discount, Discounts_Code $used_code ) : void
	{
		$discount->setTitle( Tr::_('Discount %D% for delivery price', ['D'=>$used_code->getDiscount()]) );
		$discount->setSubType( Order_Item::DISCOUNT_TYPE_DELIVERY_AMOUNT );
	}

	protected function setDiscount_delivery_percentage( Order_Item $discount, Discounts_Code $used_code ) : void
	{
		$discount->setTitle( Tr::_('Discount %D%% for delivery price', ['D'=>$used_code->getDiscount()]) );
		$discount->setSubType( Order_Item::DISCOUNT_TYPE_DELIVERY_PERCENT );
	}

	protected function setDiscount_payment_amount( Order_Item $discount, Discounts_Code $used_code )
	{
		$discount->setTitle( Tr::_('Discount %D% for payment price', ['D'=>$used_code->getDiscount()]) );
		$discount->setSubType( Order_Item::DISCOUNT_TYPE_PAYMENT_AMOUNT );
	}

	protected function setDiscount_payment_percentage( Order_Item $discount, Discounts_Code $used_code ) : void
	{
		$discount->setTitle( Tr::_('Discount %D%% for payment price', ['D'=>$used_code->getDiscount()]) );
		$discount->setSubType( Order_Item::DISCOUNT_TYPE_PAYMENT_PERCENT );
	}

	protected function setDiscount_products_amount( Order_Item $discount, Discounts_Code $used_code ) : void
	{
		$discount->setTitle( Tr::_('Discount %D% for products price', ['D'=>$used_code->getDiscount()]) );
		$discount->setSubType( Order_Item::DISCOUNT_TYPE_PRODUCTS_AMOUNT );
	}
	

	protected function setDiscount_products_percentage( Order_Item $discount, Discounts_Code $used_code ) : void
	{
		$discount->setTitle( Tr::_('Discount %D%% for products price', ['D'=>$used_code->getDiscount()]) );
		$discount->setSubType( Order_Item::DISCOUNT_TYPE_PRODUCTS_PERCENT );
	}

	protected function setDiscount_order_percentage( Order_Item $discount, Discounts_Code $used_code ) : void
	{
		$discount->setTitle( Tr::_('Discount %D% for order amount', ['D'=>$used_code->getDiscount()]) );
		$discount->setSubType( Order_Item::DISCOUNT_TYPE_TOTAL_PERCENT );
	}

	protected function setDiscount_order_amount( Order_Item $discount, Discounts_Code $used_code ) : void
	{
		$discount->setTitle( Tr::_('Discount %D%% for order amount', ['D'=>$used_code->getDiscount()]) );
		$discount->setSubType( Order_Item::DISCOUNT_TYPE_TOTAL_AMOUNT );
	}



	public function Order_saved( Order $order ) : void
	{
		$code = $this->getUsedCode();

		$code?->used( $order );

		$this->cancelUse();
	}

	public function Order_canceled( Order $order ) : void
	{
		Discounts_Code::cancelUsages( $order );
	}

	protected function _getCodeByOrderItem( Order_Item $item ) : ?Discounts_Code
	{
		if(
			$item->getType()!=Order_Item::ITEM_TYPE_DISCOUNT ||
			$item->getCode()!=static::ORDER_ITEM_CODE
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