<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;



use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_tabsJS;
use JetApplication\Delivery_Method;
use JetApplication\Discounts_Discount;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Payment_Method;
use JetApplication\Product_EShopData;


class Plugin_AddItem_Main extends Plugin {
	public const KEY = 'add_item';
	
	protected UI_tabsJS $tabs;
	/**
	 * @var Form[] $forms
	 */
	protected array $forms = [];
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		$tabs = [
			Order_Item::ITEM_TYPE_PRODUCT => Tr::_('Product'),
			Order_Item::ITEM_TYPE_GIFT    => Tr::_('Gift'),
			Order_Item::ITEM_TYPE_DISCOUNT => Tr::_('Discount'),
			
			Order_Item::ITEM_TYPE_PAYMENT => Tr::_('Payment fee'),
			Order_Item::ITEM_TYPE_DELIVERY => Tr::_('Delivery fee'),
		];
		
		$this->tabs = new UI_tabsJS('what_to_add', $tabs);
		
		foreach( $tabs as $what=>$title ) {
			$this->{"createForm_{$what}"}();
		}
		
		$this->view->setVar( 'add_item_tabs', $this->tabs );
		$this->view->setVar( 'add_item_forms', $this->forms);
	}
	
	protected function createForm_product() : void
	{
		$product_id = new Form_Field_Hidden('product_id', '');
		$qty = new Form_Field_Float('qty', 'Number of units:');
		$qty->setDefaultValue(1);
		$qty->setMinValue(1);
		$qty->setErrorMessages([
			Form_Field_Int::ERROR_CODE_OUT_OF_RANGE => 'Minimal value is: 1'
		]);
		
		$form = new Form('add_item_product', [$product_id, $qty]);
		$form->setAction( Http_Request::currentURI() );
		
		$this->forms[Order_Item::ITEM_TYPE_PRODUCT] = $form;
	}
	
	protected function catch_product( Form $form ) : bool
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$product_id = (int)$form->field('product_id')->getValue();
		$qty = $form->field('qty')->getValue();
		
		$product = Product_EShopData::get( $product_id, $item->getEshop() );
		if(!$product) {
			return false;
		}
		
		return $item->addProduct( $product, $qty )->hasChange();
	}
	
	protected function createForm_gift() : void
	{
		$product_id = new Form_Field_Hidden('product_id', '');
		$qty = new Form_Field_Float('qty', 'Number of units:');
		$qty->setDefaultValue(1);
		$qty->setMinValue(1);
		$qty->setErrorMessages([
			Form_Field_Int::ERROR_CODE_OUT_OF_RANGE => 'Minimal value is: 1'
		]);
		
		$form = new Form('add_item_gift', [$product_id, $qty]);
		$form->setAction( Http_Request::currentURI() );
		
		$this->forms[Order_Item::ITEM_TYPE_GIFT] = $form;
	}
	
	protected function catch_gift( Form $form ) : bool
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$product_id = (int)$form->field('product_id')->getValue();
		$qty = $form->field('qty')->getValue();
		
		$product = Product_EShopData::get( $product_id, $item->getEshop() );
		if(!$product) {
			return false;
		}
		
		return $item->addGift( $product, $qty )->hasChange();
	}
	
	
	protected function createForm_discount() : void
	{
		$description = new Form_Field_Input('description', 'Description: ');
		$description->setIsRequired( true );
		$description->setErrorMessages([
			Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter description'
		]);
		
		$discount_type = new Form_Field_Input('discount_type', 'Discount type: ');
		$discount_module = new Form_Field_Input('discount_module', 'Discount module: ');
		$discount_context = new Form_Field_Input('discount_context', 'Discount context: ');
		
		$discount = new Form_Field_Float('discount', 'Discount:');
		$discount->setDefaultValue(0);
		
		$form = new Form('add_item_discount', [
			$description,
			
			$discount_type,
			$discount_module,
			$discount_context,
			
			$discount
		]);
		$form->setAction( Http_Request::currentURI() );
		
		$this->forms[Order_Item::ITEM_TYPE_DISCOUNT] = $form;
	}
	
	protected function catch_discount( Form $form ) : bool
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$title = $form->field('description')->getValue();
		$discount_type = $form->field('discount_type')->getValue();
		$discount_module = $form->field('discount_module')->getValue();
		$discount_context = $form->field('discount_context')->getValue();
		$discount_value = $form->field('discount')->getValue()*-1;
		
		$discount = new Discounts_Discount();
		$discount->setDescription( $title );
		$discount->setDiscountType( $discount_type );
		$discount->setDiscountModule( $discount_module );
		$discount->setDiscountContext( $discount_context );
		$discount->setAmount( $discount_value );
		$discount->setVatRate( $item->getPricelist()->getDefaultVatRate() );
		
		
		return $item->addDiscount( $discount )->hasChange();
	}
	
	
	protected function createForm_payment() : void
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$payment_method = new Form_Field_Select('payment_method', 'Payment method: ');
		$payment_method->setSelectOptions( Payment_Method::getScopeForEShop( $item->getEshop() ) );
		$payment_method->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		
		$fee = new Form_Field_Float('fee', 'Fee:');
		$fee->setDefaultValue(0);
		
		$form = new Form('add_item_payment', [
			$payment_method,
			$fee
		]);
		$form->setAction( Http_Request::currentURI() );
		
		$this->forms[Order_Item::ITEM_TYPE_PAYMENT] = $form;
	}
	
	protected function catch_payment( Form $form ) : bool
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$method_id = (int)$form->field('payment_method')->getValue();
		$fee = $form->field('fee')->getValue();
		
		$method = Payment_Method::get( $method_id );
		if(!$method) {
			return false;
		}
		
		return $item->addPaymentFee( $method, $fee )->hasChange();
	}
	
	protected function createForm_delivery() : void
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$delivery_method = new Form_Field_Select('delivery_method', 'Delivery method: ');
		$delivery_method->setSelectOptions( Delivery_Method::getScopeForEShop( $item->getEshop() ) );
		$delivery_method->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		
		$fee = new Form_Field_Float('fee', 'Fee:');
		$fee->setDefaultValue(0);
		
		$form = new Form('add_item_delivery', [
			$delivery_method,
			$fee
		]);
		$form->setAction( Http_Request::currentURI() );
		
		$this->forms[Order_Item::ITEM_TYPE_DELIVERY] = $form;
	}
	
	protected function catch_delivery( Form $form ) : bool
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$delivery_method_id = (int)$form->field('delivery_method')->getValue();
		$fee = $form->field('fee')->getValue();
		
		$method = Delivery_Method::get( $delivery_method_id );
		
		return $item->addDeliveryFee( $method, $fee )->hasChange();
	}
	
	
	public function handle() : void
	{
		foreach($this->forms as $what=>$form) {
			if($form->catchInput()) {
				$ok = false;
				$this->view->setVar('form', $form);
				if($form->validate()) {
					$ok = $this->{"catch_{$what}"}( $form );
				}
				
				AJAX::operationResponse($ok, snippets: [
					'form_area_'.$what => $this->view->render('form/'.$what)
				]);
			}
		}
	}
}