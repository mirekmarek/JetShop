<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Checkbox;

abstract class Core_ProductListing_Filter_Flags extends ProductListing_Filter_Abstract {
	const CACHE_KEY = 'f_flags';

	protected string $key = 'flags';

	/**
	 * @var ProductListing_Filter_Flags_Flag[]
	 */
	protected array $flags = [];

	public function getTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		foreach($this->flags as $flag) {
			$id = $flag->getId();
			
			$field = new Form_Field_Checkbox('/flags/'.$id, $flag->getLabel() );
			$field->setDefaultValue( !empty($target_filter['flags'][$id]) );
			
			$form->addField( $field );
		}
	}

	public function catchTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$target_filter['flags'] = [];

		foreach($this->flags as $flag) {
			$id = $flag->getId();

			if($form->field('/flags/'.$id)->getValue()) {
				$target_filter['flags'][$id] = true;
			}
		}
	}

	public function initByTargetFilter( array &$target_filter ) : void
	{
		foreach( $this->flags as $id=> $filter ) {

			if(isset($target_filter['flags'][$id])) {
				$filter->setIsForced(true);
			}

		}
	}

	public function getStateData( &$state_data ) : void
	{
		$active = [];
		foreach( $this->flags as $id=> $filter ) {

			if($filter->isActive()) {
				$active[] = $id;
			}
		}

		$state_data['flags'] = [ 'active'=> $active ];
	}

	public function initByStateData( array $state_data ) : void
	{
		$active = $state_data['flags']['active'];

		foreach( $this->flags as $id=> $filter ) {
			$filter->setIsActive( in_array($id, $active) );
		}
	}


	public function generateCategoryTargetUrl( array &$parts ) : void
	{

		foreach( $this->flags as $flag ) {
			if( $flag->isForced() ) {
				$parts[] = $flag->getUrlParam();
			}
		}

	}

	public function generateUrl( array &$parts ) : void
	{

		foreach( $this->flags as $flag ) {
			if( $flag->isActive() ) {
				$parts[] = $flag->getUrlParam();
			}
		}

	}

	public function parseFilterUrl( array &$parts ) : void
	{
		foreach($parts as $i=>$part) {

			foreach( $this->flags as $flag ) {

				if($flag->getUrlParam()==$part) {
					unset($parts[$i]);

					if(!$flag->isForced()) {
						$flag->setIsActive(true);
					}

					continue 2;
				}
			}
		}
	}


	public function filterIsActive() : bool
	{
		foreach( $this->flags as $s) {
			if($s->isActive() || $s->isForced() ) {
				return true;
			}
		}

		return false;
	}

	public function getFilteredProductIds() : array
	{

		if($this->filtered_product_ids===null) {
			$id_map = [];

			foreach( $this->flags as $s) {
				if($s->isActive() || $s->isForced() ) {
					$id_map[] = $s->getProductIds();
				}
			}

			$this->filtered_product_ids = $this->listing->idMapIntersect( $id_map );
		}

		return $this->filtered_product_ids;
	}

	public function disableNonRelevantFilters() : void
	{
		foreach( $this->flags as $flag)
		{
			if(!$flag->getCount()) {
				$flag->setIsDisabled(true);
			}
		}
	}

	public function resetCount() : void
	{
		foreach( $this->flags as $flag)
		{
			$flag->resetCount();
		}
	}

	/**
	 * @return ProductListing_Filter_Flags_Flag[]
	 */
	public function getFilterFlags() : array
	{
		return $this->flags;
	}


	/**
	 * @return ProductListing_Filter_Flags_Flag[]
	 */
	public function getActiveFilterFlags() : array
	{
		$flags = [];

		foreach($this->flags as $flag) {
			if($flag->isActive()) {
				$flags[$flag->getId()] = $flag;
			}
		}

		return $flags;
	}

	public function prepareFilter( array $initial_product_ids ) : void
	{
		if(!$initial_product_ids) {
			return;
		}

		$map = $this->listing->cache()->get( static::CACHE_KEY );

		if($map===null) {
			$select_items = ['product_id'];
			$map = [];

			foreach($this->flags as $flag_id=>$flag) {
				$map[$flag_id] = [];

				foreach($flag->getSelectItems() as $select_item) {
					$select_items[] = $select_item;
				}
			}

			$data = Product_ShopData::dataFetchAll(
				select: $select_items,
				where: [
					'product_id'=>$initial_product_ids,
					'AND',
					$this->listing->getShop()->getWhere()
				]
			);

			foreach($data as $d) {
				$p_id = (int)$d['product_id'];

				foreach($this->flags as $flag_id=>$flag) {
					if( $flag->addToMap( $d ) ) {
						$map[$flag_id][] = $p_id;
					}
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