<?php
namespace JetShop;

class ProductListing_Filter_Flags extends Core_ProductListing_Filter_Flags {

	public function init() : void
	{
		$this->flags = [];

		$in_stock = new ProductListing_Filter_Flags_Flag( $this->listing, 'in_stock' );
		$in_stock->setLabel('In stock');
		$in_stock->setUrlParam('in_stock');
		$in_stock->setSelectItems(['stock_status']);
		$in_stock->setAnalyzer( function( array $item ) : bool {
			return ( $item['stock_status']>0 );
		} );

		$action = new ProductListing_Filter_Flags_Flag( $this->listing, 'action' );
		$action->setLabel('Action price');
		$action->setUrlParam('action-price');
		$action->setSelectItems(['action_price']);
		$action->setAnalyzer( function( array $item ) : bool {
			return ( $item['action_price']>0 );
		} );

		$this->flags[$in_stock->getId()] = $in_stock;
		$this->flags[$action->getId()] = $action;
	}



}