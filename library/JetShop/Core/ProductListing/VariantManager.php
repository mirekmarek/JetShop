<?php
namespace JetShop;

use JetApplication\ProductListing;
use JetApplication\Shops_Shop;
use JetApplication\Product;

abstract class Core_ProductListing_VariantManager
{
	const CACHE_KEY = 'f_variants';

	protected ProductListing $listing;
	protected Shops_Shop $shop;

	protected array $map = [];

	public function __construct( ProductListing $listing )
	{
		$this->listing = $listing;
		$this->shop = $listing->getShop();

		$this->init();
	}

	protected function init() : void
	{

	}

	public function prepareFilter( array $initial_product_ids ) : void
	{
		if(!$initial_product_ids) {
			return;
		}

		$cache_rec = $this->listing->cache()->get( static::CACHE_KEY );

		if($cache_rec!==null) {
			$this->map = $cache_rec;
		} else {
			$map = Product::dataFetchAll(
				select: [
					'id',
					'type',
					'variant_master_product_id',
					'variant_ids'
				],
				where: [
					'id' => $initial_product_ids,
					'AND',
					'type' => [Product::PRODUCT_TYPE_VARIANT_MASTER, Product::PRODUCT_TYPE_VARIANT]
				]
			);

			$this->map = [];

			foreach($map as $d) {
				$d['id'] = (int)$d['id'];
				$d['variant_master_product_id'] = (int)$d['variant_master_product_id'];
				$d['variant_ids'] = explode(',', $d['variant_ids']);

				foreach($d['variant_ids'] as $i=>$id) {
					$d['variant_ids'][$i] = (int)$id;
				}

				$this->map[ $d['id'] ] = $d;
			}

			$this->listing->cache()->set( static::CACHE_KEY, $this->map );
		}

	}

	public function manage( array $filtered_product_ids ) : array
	{
		if(!$filtered_product_ids) {
			return [];
		}

		$variants_map = [];
		$variants_map_by_master = [];

		foreach($filtered_product_ids as $_id) {
			if(!isset($this->map[$_id])) {
				continue;
			}

			$d = $this->map[$_id];

			if($d['type']==Product::PRODUCT_TYPE_VARIANT) {
				$variants_map[$d['id']] = $d['variant_master_product_id'];
			}

			if($d['type']==Product::PRODUCT_TYPE_VARIANT_MASTER) {
				$v_ids = [];

				foreach( $d['variant_ids'] as $id) {
					if(in_array($id, $filtered_product_ids)) {
						$v_ids[] = $id;
					}
				}

				$variants_map_by_master[$d['id']] = $v_ids;
			}

		}

		$ids = [];

		foreach( $filtered_product_ids as $id ) {
			$master_id = null;

			if(isset($variants_map[$id])) {
				$master_id = $variants_map[$id];
			}

			if(isset($variants_map_by_master[$id])) {
				$master_id = $id;
			}

			if(!$master_id) {
				$ids[$id] = $id;
				continue;
			}

			if(!isset($variants_map_by_master[$master_id])) {
				$ids[$id] = $id;
				continue;
			}

			if(count($variants_map_by_master[$master_id])==0) {
				continue;
			}

			if(count($variants_map_by_master[$master_id])==1) {
				$id = $variants_map_by_master[$master_id][0];

				$ids[$id] = $id;
				continue;
			}

			$ids[$master_id] = $master_id;
		}

		return array_keys($ids);
	}

}