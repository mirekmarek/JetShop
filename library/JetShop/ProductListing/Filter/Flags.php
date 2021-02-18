<?php
namespace JetShop;

class ProductListing_Filter_Flags extends Core_ProductListing_Filter_Flags {

	public function init() : void
	{
		$this->flags = [];

		$on_stock = new ProductListing_Filter_Flags_Flag( $this->listing, 'on_stock' );
		$on_stock->setLabel('Skladem');
		$on_stock->setUrlParam('on_stock');

		$action = new ProductListing_Filter_Flags_Flag( $this->listing, 'action' );
		$action->setLabel('V akci');
		$action->setUrlParam('v-akci');

		$this->flags[$on_stock->getId()] = $on_stock;
		$this->flags[$action->getId()] = $action;
	}

	public function prepareFilter( array $initial_product_ids ) : void
	{
		if(!$initial_product_ids) {
			return;
		}

		$map = $this->listing->cache()->get( static::CACHE_KEY );

		if($map===null) {
			$data = Product_ShopData::fetchData(
				[
					'product_id',
					'action_price',
					'stock_status'
				],
				[
					'product_id'=>$initial_product_ids,
					'AND',
					'shop_code'=>$this->listing->getShopCode()
				]
			);
			$map = [
				'skladem' => [],
				'akce' => []
			];

			foreach($data as $d) {
				$p_id = (int)$d['product_id'];

				if( $d['stock_status']>0 ) {
					$map['skladem'][] = $p_id;
				}

				if( $d['action_price']>0 ) {
					$map['akce'][] = $p_id;
				}
			}

			$this->listing->cache()->set( static::CACHE_KEY, $map );
		}

		foreach($map as $flag_id=>$product_ids) {
			if(isset( $this->flags[$flag_id])) {
				$this->flags[$flag_id]->setProductIds( $product_ids );
			}
		}

	}


}