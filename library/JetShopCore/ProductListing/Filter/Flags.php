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

			$form->addField(new Form_Field_Checkbox('/flags/'.$id, $flag->getLabel(), !empty($target_filter['flags'][$id])  ));
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


}