<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Category_Product;
use JetApplication\Category_EShopData;
use JetApplication\Product;

trait Core_Product_EShopData_Trait_Categories
{
	protected ?array $category_ids = null;
	
	protected ?array $categories = null;
	
	public function getCategoryIds() : array
	{
		if($this->category_ids===null) {
			if($this->type==Product::PRODUCT_TYPE_VARIANT) {
				$id = $this->variant_master_product_id;
			} else {
				$id = $this->entity_id;
			}
			
			$this->category_ids = Category_Product::dataFetchCol(
				select: ['category_id'],
				where:['product_id'=>$id]
			);
		}
		
		return $this->category_ids;
	}
	
	/**
	 * @return Category_EShopData[]
	 */
	public function getCategories() : array
	{
		if($this->categories===null) {
			$this->categories = Category_EShopData::getActiveList( $this->getCategoryIds() );
		}
		
		return $this->categories;
	}

}