<?php
namespace JetShop;

use JetApplication\Product_Similar;

trait Core_Product_Trait_Similar
{
	
	/**
	 * @var Product_Similar[]
	 */
	protected ?array $similar_products = null;
	
	
	/**
	 * @return Product_Similar[]
	 */
	public function getSimilar():array
	{
		if($this->similar_products===null ) {
			$items = Product_Similar::fetchInstances(['product_id'=>$this->id]);
			$items->getQuery()->setOrderBy('sort_order');
			
			$this->similar_products = [];
			
			foreach( $items as $item ) {
				$this->similar_products[(int)$item->getSimilarProductId()] = $item;
			}
		}
		
		return $this->similar_products;
	}
	
	
	
	public function addSimilar( array $ids ) : void
	{
		$this->getSimilar();
		
		$new_ids = [];
		foreach($ids as $id) {
			$id = (int)$id;
			if(
				!$id ||
				$id==$this->id ||
				isset( $this->similar_products[$id])
			) {
				continue;
			}
			
			$new_ids[] = $id;
		}
		
		if(!$new_ids) {
			return;
		}
		
		$group = array_keys($this->similar_products);
		if(!$group) {
			$group = [$this->id];
		}
		$group = array_merge( $group, $new_ids );
		
		$this->_saveGroup($group);
	}
	
	public function sortSimilar( array $ids) : void
	{
		$this->getSimilar();
		foreach($ids as $i=>$id) {
			$ids[$i] = (int)$id;
		}
		
		if(array_diff(array_keys($this->similar_products), $ids)) {
			return;
		}
		
		$this->_saveGroup($ids);
	}
	
	public function deleteSimilar( int $id ) : void
	{
		
		$this->getSimilar();
		if(
			$id==$this->id ||
			!$this->similar_products[$id]
		) {
			return;
		}
		
		$group = [];
		foreach( $this->similar_products as $s_id=> $s) {
			if($s_id!=$id) {
				$group[] = $s_id;
			}
		}
		
		if(count($group)==1) {
			$group = [];
		}
		
		$this->_saveGroup($group);
	}
	
	protected function _saveGroup( array $group) : void
	{
		foreach( $this->similar_products as $id=> $sim) {
			Product_Similar::dataDelete([
				'product_id' => $id,
				'OR',
				'similar_product_id' => $id
			]);
		}
		
		if(!$group) {
			return;
		}
		
		$source = [];
		$order = 0;
		
		foreach($group as $id) {
			$order++;
			$source[$id] = $order;
		}
		
		foreach(array_keys($source) as $product_id) {
			foreach($source as $similar_product_id=>$order) {
				$item = new Product_Similar();
				$item->setProductId( $product_id );
				$item->setSimilarProductId( $similar_product_id );
				$item->setSortOrder( $order );
				
				$item->save();
			}
		}
	}
	
	
}