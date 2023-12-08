<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Exception;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Int;
use Jet\Tr;

use JetApplication\Product_SetItem;
use JetApplication\Product;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

trait Core_Product_Trait_Set {


	/**
	 * @var Product_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_SetItem::class,
	)]
	protected array $set_items = [];


	protected ?Form $_set_setup_form = null;

	protected ?Form $_set_add_item_form = null;
	
	
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
		if(isset( $this->set_items[$item_product_id])) {
			return $this->set_items[$item_product_id];
		}

		$product_type = static::getProductType( $item_product_id );
		
		if(!$product_type) {
			throw new Exception('Unknown product '.$item_product_id);
		}

		if(
			$product_type == static::PRODUCT_TYPE_VARIANT_MASTER ||
			$product_type == static::PRODUCT_TYPE_SET
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
			$this->_set_setup_form = $this->createForm(form_name: 'set_setup_form', only_fields: []);

			foreach($this->getSetItems() as $set_item) {


				$count = new Form_Field_Int('/p'.$set_item->getItemProductId().'/count', 'Count' );
				$count->setDefaultValue( $set_item->getCount() );
				$count->setFieldValueCatcher( function(int $value) use ($set_item) : void {
					$set_item->setCount($value);
				} );
				$sort_order = new Form_Field_Int('/p'.$set_item->getItemProductId().'/sort_order', 'Sort order' );
				$sort_order->setDefaultValue( $set_item->getSortOrder() );
				$sort_order->setFieldValueCatcher( function(int $value) use ($set_item) : void {
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


	public function getSetAddItemForm() : Form
	{
		if(!$this->_set_add_item_form) {

			$product_id = new Form_Field_Hidden('product_id');
			$product_id->setValidator(function( Form_Field_Hidden $field ) {
				$id = (int)$field->getValue();

				if(!$id) {
					return false;
				}

				$product_type = static::getProductType($id);
				if(!$product_type) {
					return false;
				}

				if(
					$product_type == static::PRODUCT_TYPE_VARIANT_MASTER ||
					$product_type == static::PRODUCT_TYPE_SET
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
		$set_items = $this->getSetItems();
		
		//TODO:
	}

	public function actualizeSetItem() : void
	{
		//TODO:
	}
}