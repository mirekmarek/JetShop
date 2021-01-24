<?php
namespace JetShop;

use Jet\Data_Paginator;

abstract class Core_ProductListing_Pagination {

	protected ?ProductListing $listing = null;

	protected string $shop_id = '';

	protected int $current_page_no = 1;

	protected int $items_per_page = 18;

	protected Data_Paginator|null|bool $_paginator = null;

	public function __construct( ProductListing $listing )
	{
		$this->listing = $listing;
		$this->shop_id = $listing->getShopId();

		$this->init();
	}

	abstract protected function init();

	abstract public function getPaginationUrlParam() : string;

	public function getItemsPerPage() : int
	{
		return $this->items_per_page;
	}

	public function setItemsPerPage( int $items_per_page ) : void
	{
		$this->items_per_page = $items_per_page;
		$this->_paginator = false;
	}

	public function getPaginator() : Data_Paginator
	{
		if(!$this->_paginator) {
			$base_url = $this->listing->generateUrl();
			$prefix = $this->getPaginationUrlParam().'_';

			$this->_paginator = new Data_Paginator(
				$this->current_page_no,
				$this->items_per_page,
				function( $page_no ) use ($base_url, $prefix) {
					if($page_no<=1) {
						return $base_url;
					}

					return $base_url.'/'.$prefix.$page_no;
				}
			);

			$this->_paginator->setData( $this->listing->getFilteredProductIds() );

			$this->current_page_no = $this->_paginator->getCurrentPageNo();
		}

		return $this->_paginator;
	}

	public function generateUrl( array &$parts ) : void
	{
		$paginator = $this->getPaginator();

		if( $paginator->getCurrentPageNo()>1 ) {
			$parts[] = $this->getPaginationUrlParam().'_'.$paginator->getCurrentPageNo();
		}
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		$prefix = $this->getPaginationUrlParam().'_';


		foreach($parts as $i=>$part) {
			if(stripos($part, $prefix)===0) {
				unset($parts[$i]);

				$page_no = (int)explode('_', $part)[1];

				$this->current_page_no = $page_no;

			}
		}

	}

	public function setPageNo( int $page_no ) : void
	{
		$this->current_page_no = $page_no;
	}

	public function getStateData( array &$state_data ) : void
	{
		$state_data['pagination'] = [
			'page_no' => $this->getPaginator()->getCurrentPageNo()
		];

	}

	public function initByStateData( array $state_data ) : void
	{
		if(isset($state_data['pagination']['page_no'])) {
			$this->setPageNo( $state_data['pagination']['page_no'] );
		}
	}


	public function getProductIds() : iterable
	{
		return $this->getPaginator()->getData();
	}

	public function getPagesCount() : int
	{
		return $this->getPaginator()->getPagesCount();
	}

	public function getCurrentPageNo() : int
	{
		return $this->getPaginator()->getCurrentPageNo();
	}

	public function getPrevPageNo() : int|null
	{
		return $this->getPaginator()->getPrevPageNo();
	}

	public function getNextPageNo() : int|null
	{
		return $this->getPaginator()->getNextPageNo();
	}

	public function getDataIndexStart() : int
	{
		return $this->getPaginator()->getDataIndexStart();
	}

	public function getDataIndexEnd() : int
	{
		return $this->getPaginator()->getDataIndexEnd();
	}

	public function getShowFrom() : int
	{
		return $this->getPaginator()->getShowFrom();
	}

	public function getShowTo() : int
	{
		return $this->getPaginator()->getShowTo();
	}

	public function getCurrentPageNoIsInRange() : bool
	{
		return $this->getPaginator()->getCurrentPageNoIsInRange();
	}

	public function getPrevPageURL() : null|bool
	{
		return $this->getPaginator()->getPrevPageURL();
	}

	public function getNextPageURL() : null|string
	{
		return $this->getPaginator()->getNextPageURL();
	}

	public function getLastPageURL() : null|string
	{
		return $this->getPaginator()->getLastPageURL();
	}

	public function getFirstPageURL() : null|string
	{
		return $this->getPaginator()->getFirstPageURL();
	}

	public function getPagesURL() : array
	{
		return $this->getPaginator()->getPagesURL();
	}

	public function getPageURL( int $page_no ) : string
	{
		$url = $this->listing->generateUrl();

		if($page_no>1) {
			return $url.'/'.$this->getPaginationUrlParam().'_'.$page_no;
		}

		return $url;
	}

	public function displayLinks(
						callable $normal,
						callable $selected
					) : string
	{
		$page_URLs = $this->getPagesURL();

		if(!$page_URLs) {
			return '';
		}

		$current_page_no = $this->getCurrentPageNo();
		$pages_count = $this->getPagesCount();


		$generatePageUrl = function( $page_no ) use ($normal, $selected) {
			if($this->getCurrentPageNo()==$page_no) {
				return $selected($page_no);
			} else {
				return $normal($page_no);
			}

		};


		$page_window_start = $current_page_no - 2;
		$page_window_end = $current_page_no + 2;

		$dots_start = false;
		$dots_end = false;

		if($page_window_start<=2) {
			$page_window_end = $page_window_end+(-1*$page_window_start)+1;

			$page_window_start = 2;
		} else {
			$dots_start = true;
		}

		if($page_window_end>=($pages_count-1)) {
			$page_window_end = ($pages_count-1);
		} else {
			$dots_end = true;
		}

		$display_links_string = '';
		$display_links_string .= $generatePageUrl( 1 );

		if($dots_start) {
			$display_links_string .= '..';
		}

		if( ($page_window_end-$page_window_start)>0 ) {
			for( $p=$page_window_start; $p<=$page_window_end;$p++ ) {
				$display_links_string .= $generatePageUrl( $p );
			}

		}

		if($dots_end) {
			$display_links_string .= '..';
		}

		$display_links_string .= $generatePageUrl( $pages_count );


		return $display_links_string;
	}

	public function getNextPageCountProducts() : int
	{
		$next_products_count = $this->items_per_page;

		$paginator = $this->getPaginator();

		if($next_products_count>$paginator->getDataIndexEnd()) {
			return $paginator->getDataIndexEnd() - $paginator->getDataIndexStart();
		}

		return $next_products_count;
	}

}