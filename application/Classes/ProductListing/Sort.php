<?php
namespace JetApplication;

use Jet\Tr;
use JetShop\Core_ProductListing_Sort;

class ProductListing_Sort extends Core_ProductListing_Sort {

	protected array|null $map = null;

	public static function getAutoAppendProductFilterEditForm_SortOptionsScope() : array
	{
		return [
			'predefined'     => Tr::_('Predefined',     [], Category::getManageModuleName()),
			'cheapest'       => Tr::_('Cheapest',       [], Category::getManageModuleName()),
			'most_expensive' => Tr::_('Most expensive', [], Category::getManageModuleName()),
			'reviews'        => Tr::_('By reviews',     [], Category::getManageModuleName()),
		];
	}

	public function getSortUrlParam() : string
	{
		return 'sort';
	}

	public function prepareFilter( array $initial_product_ids ) : void
	{
		if(!$initial_product_ids) {
			return;
		}

		$this->map = $this->listing->cache()->get( static::CACHE_KEY );

		if($this->map===null) {
			$data = Product_ShopData::dataFetchAll(
				select: [
					'product_id',
					'final_price'
				],
				where: [
					'product_id'=>$initial_product_ids,
					'AND',
					$this->listing->getShop()->getWhere()
				]
			);

			$this->map = [];

			foreach($data as $d) {
				$p_id = (int)$d['product_id'];

				$this->map[$p_id] = $d;
			}

			$this->listing->cache()->set( static::CACHE_KEY, $this->map );
		}

	}

	protected function _sort( array $ids, string $key, bool $descending ) : array
	{
		$sort_map = [];

		foreach($ids as $id) {
			$sort_map[$id] = $this->map[$id][$key];
		}

		if($descending) {
			asort( $sort_map );
		} else {
			arsort( $sort_map );
		}

		return array_keys($sort_map);
	}

	public function init() : void
	{
		$this->sort_options = [];

		$predefined = new ProductListing_Sort_Option( 'predefined' );
		$predefined->setIsDefault(true);
		$predefined->setLabel(Tr::_('Default'));
		$predefined->setUrlParam('default');
		$predefined->setSorter( function( array $ids ) {
			//TODO:
			return $ids;
		} );



		$cheapest = new ProductListing_Sort_Option( 'cheapest' );
		$cheapest->setLabel(Tr::_('The cheapest first'));
		$cheapest->setUrlParam('cheapest');
		$cheapest->setSorter( function( array $ids ) {
			return $this->_sort( $ids, 'final_price', false );
		} );



		$most_expensive = new ProductListing_Sort_Option( 'most_expensive' );
		$most_expensive->setLabel(Tr::_('The most expensive first'));
		$most_expensive->setUrlParam('most-expensive');
		$most_expensive->setSorter( function( array $ids ) {
			return $this->_sort( $ids, 'final_price', true );
		} );

		$reviews = new ProductListing_Sort_Option( 'reviews' );
		$reviews->setLabel(Tr::_('According to reviews'));
		$reviews->setUrlParam('reviews');
		$reviews->setSorter( function( array $ids ) {
			//TODO:
			return $ids;
		} );


		$this->sort_options[$predefined->getId()] = $predefined;
		$this->sort_options[$reviews->getId()] = $reviews;
		$this->sort_options[$cheapest->getId()] = $cheapest;
		$this->sort_options[$most_expensive->getId()] = $most_expensive;

	}

}