<?php
namespace JetShop;

use Jet\Form;

use JetApplication\Product;

trait Core_ProductListing_AutoAppendProductFilter
{
	protected ?Form $_target_filter_edit_form = null;
	
	public function getAutoAppendProductFilterEditForm( array &$target_filter ) : Form
	{
		if( !$this->_target_filter_edit_form ) {
			$this->_target_filter_edit_form = new Form( 'target_filter_edit_form', [] );
			
			foreach( $this->filters as $filter ) {
				$filter->initAutoAppendFilter( $target_filter );
			}
			
			foreach( $this->filters as $filter ) {
				$filter->getAutoAppendProductFilterEditForm( $this->_target_filter_edit_form );
			}
			
			$this->_target_filter_edit_form->setDoNotTranslateTexts( true );
		}
		
		return $this->_target_filter_edit_form;
	}
	
	public function catchAutoAppendProductFilterEditForm( array &$target_filter ) : bool
	{
		$form = $this->getAutoAppendProductFilterEditForm( $target_filter );
		if( !$form->catchInput() || !$form->validate() ) {
			return false;
		}
		
		foreach( $this->filters as $filter ) {
			$filter->catchAutoAppendFilterEditForm( $form, $target_filter );
		}
		
		return true;
	}
	
	public function getAutoAppendProductIds() : array
	{
		$kind = $this->category->getKindOfProduct();
		if(!$kind) {
			return [];
		}

		$filter_rules = $this->category->getAutoAppendProductsFilter();
		if(!$filter_rules) {
			return [];
		}
		
		$this->cache->disable();
		
		$product_ids = Product::getIdsByKind( $kind );
		if(!$product_ids) {
			return [];
		}
		
		
		foreach( $this->filters as $filter ) {
			$filter->initAutoAppendFilter( $filter_rules );
		}
		
		foreach( $this->filters as $filter ) {
			$filter->prepareFilter( $product_ids );
		}
		
		$id_map = [];
		$some_filter_is_active = false;
		foreach( $this->filters as $filter ) {
			if( $filter->filterIsActive() ) {
				$some_filter_is_active = true;
				
				$ids = $filter->getFilteredProductIds();
				
				if( !count( $ids ) ) {
					return [];
				}
				
				$id_map[] = $ids;
			}
		}
		
		
		if(!$some_filter_is_active) {
			return $product_ids;
		}
		
		return $this->idMapIntersect($id_map);
	}

}