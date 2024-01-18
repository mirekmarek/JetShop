<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Exception;

use JetApplication\Product_Parameter_Value;
use JetApplication\Product_SetItem;
use JetApplication\Product;
use JetApplication\Shops;

trait Core_Product_Trait_Set {


	/**
	 * @var Product_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_SetItem::class,
	)]
	protected array $set_items = [];
	
	
	
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
		$set_item->setSortOrder( count($this->set_items)+1 );

		$this->set_items[$item_product_id] = $set_item;
		
		$set_item->save();
		$this->actualizeSet();

		return $set_item;
	}

	public function removeSetItem( int $product_id ) : void
	{
		if(isset($this->set_items[$product_id])) {
			$this->set_items[$product_id]->delete();
			unset($this->set_items[$product_id]);
			$this->actualizeSet();
		}
	}
	


	public function actualizeSet() : void
	{
		if($this->type!=static::PRODUCT_TYPE_SET) {
			return;
		}
		foreach(Shops::getList() as $shop) {
			$this->getShopData($shop)->actualizeSet();
		}
		
		Product_Parameter_Value::syncSetItemsParameters(
			$this->id,
			array_keys($this->set_items)
		);
		
	}
	

	public function actualizeSetItem() : void
	{
		$sets = Product_SetItem::dataFetchCol(select:['product_id'], where: ['item_product_id'=>$this->id]);
		
		foreach($sets as $set_id) {
			$set_product = static::load(['id'=>$set_id]);
			$set_product?->actualizeSet();
		}
	}
}