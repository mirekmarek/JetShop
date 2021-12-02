<?php
namespace JetShop;

abstract class Core_ProductListing_Cache {

	protected ProductListing $listing;
	protected Shops_Shop $shop;

	protected bool $enabled = true;

	protected string $cache_key = '';

	protected array $cache_rec= [];

	protected bool $save_handler_registered = false;


	public function __construct( ProductListing $listing )
	{
		$this->listing = $listing;
		$this->shop = $listing->getShop();

		$this->init();
	}


	public function init() : void
	{
		$this->enabled = Cache::isEnabled();
	}

	public function prepare( array $initial_product_ids ) : void
	{
		if(!$initial_product_ids) {
			$this->enabled = false;
		}

		$this->cache_key = 'listing:'.$this->shop->getKey().':'.md5(implode(',', $initial_product_ids));

		$cache_rec = Cache::load( $this->cache_key );

		if(!$cache_rec) {
			$cache_rec = [];
		}

		$this->cache_rec = $cache_rec;
	}

	public function get( string $item_key ) : mixed
	{
		if(!$this->enabled) {
			return null;
		}

		if(!array_key_exists($item_key, $this->cache_rec)) {
			return null;
		}

		return $this->cache_rec[$item_key];
	}

	public function set( string $item_key, mixed $data ) : void
	{
		if(!$this->enabled) {
			return;
		}

		$this->cache_rec[$item_key] = $data;

		if(!$this->save_handler_registered) {
			register_shutdown_function( [$this, 'save'] );
			$this->save_handler_registered = true;
		}
	}

	public function save() : void
	{
		if(!$this->enabled) {
			return;
		}

		Cache::save( $this->cache_key, $this->cache_rec );
	}


}