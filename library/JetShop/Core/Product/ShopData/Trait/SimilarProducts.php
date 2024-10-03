<?php
namespace JetShop;

use JetApplication\Product_Similar;

trait Core_Product_ShopData_Trait_SimilarProducts
{
	/**
	 * @return static[]
	 */
	public function getSimilarProducts() : array
	{
		$similar_product_ids = Product_Similar::dataFetchCol(
			select:['similar_product_id'],
			where:['product_id'=>$this->entity_id],
			order_by:['sort_order']
		);
		
		if(!$similar_product_ids) {
			return [];
		}
		
		$s = static::getActiveList( $similar_product_ids );
		
		$result = [];
		
		foreach($similar_product_ids as $id) {
			if(!isset($d[$id])) {
				continue;
			}
			
			$result[$id] = $s[$id];
		}
		
		return $result;
	}
	
}