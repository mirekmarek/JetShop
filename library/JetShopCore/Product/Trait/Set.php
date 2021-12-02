<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Exception;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Int;
use Jet\Form_Field_Select;
use Jet\Tr;

trait Core_Product_Trait_Set {


	/**
	 * @var Product_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_SetItem::class,
		form_field_type: false
	)]
	protected array $set_items = [];

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: Form::TYPE_SELECT,
		form_field_label: 'How to handle set:',
		form_field_get_select_options_callback: [
			self::class,
			'getSetStrategyScope'
		],
		form_field_error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select set strategy',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select set strategy'
		]
	)]
	protected string $set_strategy = '';

	/**
	 * @var float
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		form_field_type: Form::TYPE_FLOAT,
		form_field_label: 'Set discount:'
	)]
	protected float $set_discount = 0.0;


	protected ?Form $_set_setup_form = null;

	protected ?Form $_set_add_item_form = null;

	public static function getSetStrategyScope() : array
	{
		return [
			Product::SET_STRATEGY_CALCULATED     => Tr::_('Price is calculated', [], Product::getManageModuleName()),
			Product::SET_STRATEGY_FIXED          => Tr::_('Price is fixed', [], Product::getManageModuleName()),
			Product::SET_STRATEGY_DISCOUNT       => Tr::_('Price is calculated and discount is used', [], Product::getManageModuleName()),
		];

	}

	/**
	 * @param string $value
	 */
	public function setSetStrategy( string $value ) : void
	{
		/**
		 * @var Product $this
		 */
		$this->set_strategy = $value;
	}

	/**
	 * @return string
	 */
	public function getSetStrategy() : string
	{
		/**
		 * @var Product $this
		 */
		return $this->set_strategy;
	}

	/**
	 * @param float $value
	 */
	public function setSetDiscount( float $value ) : void
	{
		/**
		 * @var Product $this
		 */
		$this->set_discount = $value;
	}

	/**
	 * @return float
	 */
	public function getSetDiscount() : float
	{
		/**
		 * @var Product $this
		 */
		return $this->set_discount;
	}

	/**
	 * @return Product_SetItem[]
	 */
	public function getSetItems(): iterable
	{
		/**
		 * @var Product $this
		 */
		return $this->set_items;
	}

	public function addSetItem( int $item_product_id ) : Product_SetItem
	{
		/**
		 * @var Product $this
		 */
		if(isset( $this->set_items[$item_product_id])) {
			return $this->set_items[$item_product_id];
		}


		$product = Product::get($item_product_id);
		if(!$product) {
			throw new Exception('Unknown product '.$item_product_id);
		}

		if(
			$product->getType() == Product::PRODUCT_TYPE_VARIANT_MASTER ||
			$product->getType() == Product::PRODUCT_TYPE_SET
		) {
			throw new Exception('Product '.$item_product_id.' can\'t be added as a set item - unsupported product type');
		}


		$set_item =  new Product_SetItem();
		$set_item->setProductId( $this->id );
		$set_item->setItemProductId( $item_product_id );
		$set_item->setCount( 1 );
		$set_item->setSortOrder( 99 );

		$this->set_items[$item_product_id] = $set_item;

		$this->setType( Product::PRODUCT_TYPE_SET );

		return $set_item;
	}

	public function removeSetItem( int $product_id ) : void
	{
		if(isset($this->set_items[$product_id])) {
			unset($this->set_items[$product_id]);
		}
	}


	public function getSetSetupForm() : Form
	{
		if(!$this->_set_setup_form) {
			$this->_set_setup_form = $this->getForm('set_setup_form', [
				'set_strategy',
				'set_discount',
			]);

			foreach($this->getSetItems() as $set_item) {


				$count = new Form_Field_Int('/p'.$set_item->getItemProductId().'/count', 'Count', $set_item->getCount() );
				$count->setCatcher( function(int $value) use ($set_item) : void {
					$set_item->setCount($value);
				} );
				$sort_order = new Form_Field_Int('/p'.$set_item->getItemProductId().'/sort_order', 'Sort order', $set_item->getSortOrder() );
				$sort_order->setCatcher( function(int $value) use ($set_item) : void {
					$set_item->setSortOrder( $value );
				} );

				$this->_set_setup_form->addField($count);
				$this->_set_setup_form->addField($sort_order);

			}


		}

		return $this->_set_setup_form;
	}

	public function catchSetSetupForm() : bool
	{
		/**
		 * @var Product $this
		 */
		$edit_form = $this->getSetSetupForm();
		if(!$edit_form->catch()) {
			return false;
		}

		$this->setType( Product::PRODUCT_TYPE_SET );
		$this->actualizeSet();

		return true;
	}

	protected function _setupForm_set( Form $form ) : void
	{
		$form->removeField('set_strategy');
		$form->removeField('set_discount');

		if($this->getSetStrategy()!=Product::SET_STRATEGY_FIXED) {
			foreach( Shops::getList() as $shop ) {
				$shop_key = $shop->getKey();

				$form->field('/shop_data/'.$shop_key.'/vat_rate')->setIsReadonly(true);
				$form->field('/shop_data/'.$shop_key.'/standard_price')->setIsReadonly(true);
				$form->field('/shop_data/'.$shop_key.'/action_price')->setIsReadonly(true);
				$form->field('/shop_data/'.$shop_key.'/action_price_valid_from')->setIsReadonly(true);
				$form->field('/shop_data/'.$shop_key.'/action_price_valid_till')->setIsReadonly(true);
				$form->field('/shop_data/'.$shop_key.'/sale_price')->setIsReadonly(true);
				$form->field('/shop_data/'.$shop_key.'/reset_sale_after_sold_out')->setIsReadonly(true);


			}
		}
	}

	public function getSetAddItemForm() : Form
	{
		if(!$this->_set_add_item_form) {

			$product_id = new Form_Field_Hidden('product_id');
			$product_id->setValidator(function( Form_Field_Hidden $field ) {
				$id = (int)$field->getValue();

				if(!$id) {
					return false;
				}

				$product = Product::get($id);
				if(!$product) {
					return false;
				}

				if(
					$product->getType() == Product::PRODUCT_TYPE_VARIANT_MASTER ||
					$product->getType() == Product::PRODUCT_TYPE_SET
				) {
					return false;
				}

				return true;
			});

			$this->_set_add_item_form = new Form('set_add_item_form', [$product_id]);
		}

		return $this->_set_add_item_form;
	}

	public function catchSetAddItemForm() : bool
	{
		$form = $this->getSetAddItemForm();
		if(!$form->catch()) {
			return false;
		}

		$id = $form->field('product_id')->getValue();

		$this->addSetItem( $id );
		$this->actualizeSet();

		return true;
	}

	public function actualizeSet() : void
	{
		//TODO:
	}

	public function actualizeSetItem() : void
	{
		//TODO:
	}
}