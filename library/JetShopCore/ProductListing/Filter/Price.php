<?php
namespace JetShop;
use Jet\Form;

abstract class Core_ProductListing_Filter_Price extends ProductListing_Filter_Abstract {
	const CACHE_KEY = 'f_price';

	protected string $key = 'price';

	protected ?float $lowest_price = null;

	protected ?float $highest_price = null;

	protected array $price_map = [];

	protected bool $is_active = false;

	protected int $from = 0;

	protected int $to = 0;

	protected function init() : void
	{
	}

	abstract public function getFilterUrlParam() : string;

	public function getTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
	}

	public function catchTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
	}

	public function initByTargetFilter( array &$target_filter ) : void
	{
	}

	public function getStateData( &$state_data ) : void
	{
		$state_data['price'] = [ 
			'is_active'=> $this->is_active,
			'from' => $this->from,
			'to' => $this->to
		];
		
	}

	public function initByStateData( array $state_data ) : void
	{
		if($state_data['price']['is_active']) {
			$this->is_active = true;
			$this->from = (float)$state_data['price']['from'];
			$this->to = (float)$state_data['price']['to'];
		} else {
			$this->is_active = false;
			$this->from = 0;
			$this->to = 0;
		}
	}

	public function generateCategoryTargetUrl( array &$parts ) : void
	{
	}

	public function generateUrl( array &$parts ) : void
	{
		if(!$this->is_active) {
			return;
		}

		$parts[] = $this->getFilterUrlParam().'_'.$this->from.'+'.$this->to;
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		$prefix = $this->getFilterUrlParam().'_';

		foreach($parts as $i=>$part) {
			if(stripos($part, $prefix)===0) {
				unset($parts[$i]);

				$from_to = explode('_', $part)[1];
				$from_to = explode('+', $from_to);

				$this->is_active = true;
				$this->from = $from_to[0];
				$this->to = $from_to[1];

			}
		}

	}

	public function prepareFilter( array $initial_product_ids ) : void
	{
		if(!$initial_product_ids) {
			return;
		}

		$cache_rec = $this->listing->cache()->get( static::CACHE_KEY );


		if($cache_rec!==null) {
			$this->price_map = $cache_rec;
		} else {
			$this->price_map = [];

			$data = Product_ShopData::fetchData(
				[
					'product_id',
					'final_price'
				],
				[
					'product_id'=>$initial_product_ids,
					'AND',
					'shop_id'=>$this->listing->getShopId()
				]
			);

			foreach( $data as $d ) {

				$product_id = (int)$d['product_id'];
				$price = (float)$d['final_price'];

				$this->price_map[$product_id] = $price;
			}

			asort($this->price_map);

			$this->listing->cache()->set( static::CACHE_KEY, $this->price_map );
		}


		$this->lowest_price = current( $this->price_map );
		$this->highest_price = end( $this->price_map );

	}

	public function filterIsActive() : bool
	{
		return $this->is_active;
	}

	public function getFilteredProductIds() : array
	{

		if($this->filtered_product_ids===null) {
			$this->filtered_product_ids = [];

			foreach($this->price_map as $id=>$price) {
				if(
					$price>=$this->from &&
					$price<=$this->to
				) {
					$this->filtered_product_ids[] = $id;
				}
			}

		}


		return $this->filtered_product_ids;
	}

	public function getLowestPrice() : float
	{
		return $this->lowest_price;
	}

	public function setLowestPrice( float $lowest_price ) : void
	{
		$this->lowest_price = $lowest_price;
	}

	public function getHighestPrice() : float
	{
		return $this->highest_price;
	}

	public function setHighestPrice( float $highest_price ) : void
	{
		$this->highest_price = $highest_price;
	}

	public function isActive() : bool
	{
		return $this->is_active;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function getFrom() : int
	{
		if(!$this->is_active) {
			return $this->lowest_price;
		}

		return $this->from;
	}

	public function setFrom( int $from ) : void
	{
		$this->from = $from;
	}

	public function getTo() : int
	{
		if(!$this->is_active) {
			return $this->highest_price;
		}

		return $this->to;
	}

	public function setTo( int $to ) : void
	{
		$this->to = $to;
	}

	abstract public function getFilterDecimalPlaces() : int;

	abstract public function getFilterStep() : int;

	public function disableNonRelevantFilters() : void
	{
	}

	public function resetCount() : void
	{
	}

}