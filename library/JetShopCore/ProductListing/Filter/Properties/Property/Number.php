<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Checkbox;
use Jet\Mvc;
use Jet\Mvc_View;
use Jet\Tr;

/**
 *
 */
abstract class Core_ProductListing_Filter_Properties_Property_Number extends ProductListing_Filter_Properties_Property
{
	protected float $filter_min = 0.0;

	protected float $filter_max = 0.0;

	protected ?float $min_value = null;

	protected ?float $max_value = null;

	protected array $data_map = [];

	public function getFilterMin() : float
	{
		return $this->filter_min;
	}

	public function setFilterMin( float $filter_min ) : void
	{
		$this->filter_min = $filter_min;
	}

	public function getFilterMax() : float
	{
		return $this->filter_max;
	}

	public function setFilterMax( float $filter_max ) : void
	{
		$this->filter_max = $filter_max;
	}

	public function getMinValue() : float
	{
		return $this->min_value;
	}

	public function getMaxValue() : float
	{
		return $this->max_value;
	}

	public function activateFilter( int|float $min, int|float $max ) : void
	{
		$this->is_active = true;
		$this->filter_min = $min;
		$this->filter_max = $max;
	}

	public function getTargetFilterEditForm( Form $form, array $target_filter ) : void
	{
		$p_id = $this->property->getId();

		if(!isset($target_filter['properties'])) {
			$target_filter['properties'] = [];
		}

		if(!isset($target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id] = [
				'disable_filter' => false,
				'min' => 0,
				'max' => 0
			];
		}

		if(!array_key_exists('disable_filter', $target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id]['disable_filter'] = false;
		}
		if(!array_key_exists('min', $target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id]['min'] = 0;
		}
		if(!array_key_exists('max', $target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id]['max'] = 0;
		}


		$disable_filter = new Form_Field_Checkbox('/properties/'.$p_id.'/disable_filter', Tr::_('Disable filter'), $target_filter['properties'][$p_id]['disable_filter']);
		$min = new Form_Field_Float('/properties/'.$p_id.'/min', 'minimum:', $target_filter['properties'][$p_id]['min']);
		$max = new Form_Field_Float('/properties/'.$p_id.'/max', 'maximum:', $target_filter['properties'][$p_id]['max']);

		$form->addField($disable_filter);
		$form->addField($min);
		$form->addField($max);

	}

	public function catchTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$p_id = $this->property->getId();

		$min = $form->field('/properties/'.$p_id.'/min')->getValue();
		$max = $form->field('/properties/'.$p_id.'/max')->getValue();
		$disable_filter = $form->field('/properties/'.$p_id.'/disable_filter')->getValue();

		if($disable_filter || $min!=0 || $max!=0) {
			$target_filter['properties'][$p_id] = [
				'disable_filter' => $disable_filter,
				'min' => $min,
				'max' => $max
			];

		}
	}

	public function initByTargetFilter( array $target_filter ) : void
	{
		$p_id = $this->property->getId();

		if(!isset($target_filter['properties'][$p_id])) {
			return;
		}

		$fd = $target_filter['properties'][$p_id];

		if(!empty($fd['disable_filter'])) {
			$this->setIsDisabled(false);
			return;
		}

		if(
			!empty($fd['min']) ||
			!empty($fd['max'])
		) {
			$this->setIsForced( true );
			$this->setFilterMin( $fd['min'] );
			$this->setFilterMax( $fd['max'] );
		}
	}

	public function getStateData() : array
	{
		return [
			'is_active' => $this->isActive(),
			'filter_min' => $this->filter_min,
			'filter_max' => $this->filter_max,
		];
	}

	public function initByStateData( array $state_data ) : void
	{
		$this->setIsActive( $state_data['is_active'] );
		$this->setFilterMin( $state_data['filter_min'] );
		$this->setFilterMax( $state_data['filter_max'] );
	}


	public function generateCategoryTargetUrlPart() : string
	{
		if($this->isForced()) {
			return $this->property->getShopData($this->listing->getShopCode())->getUrlParam().'_'.$this->filter_min.'+'.$this->filter_max;
		} else {
			return '';
		}
	}

	public function generateUrlPart() : string
	{
		if($this->isActive()) {
			return $this->property->getShopData($this->listing->getShopCode())->getUrlParam().'_'.$this->filter_min.'+'.$this->filter_max;
		} else {
			return '';
		}
	}


	public function parseFilterUrl( array &$parts ) : void
	{
		$shop_code = $this->listing->getShopCode();
		$prefix = $this->property->getShopData($shop_code)->getUrlParam().'_';

		foreach($parts as $i=>$part) {
			if(stripos($part, $prefix)===0) {
				unset($parts[$i]);

				$min_max = explode('_', $part)[1];
				$min_max = explode('+', $min_max);

				if(!$this->isForced()) {
					$this->setIsActive(true);

					$this->filter_min = $min_max[0];
					$this->filter_max = $min_max[1];
				}

			}
		}
	}

	public function prepareFilter( array $data_map ) : void
	{
		foreach($data_map as $p_id=>$d) {
			$value = (float)$d[0];

			if($this->min_value===null || $value<$this->min_value) {
				$this->min_value = $value;
			}
			if($this->max_value===null || $value>$this->max_value) {
				$this->max_value = $value;
			}

			$this->data_map[$p_id] = $value;
		}

	}

	public function generateFilterUrlPart() : string
	{
		if($this->isActive()) {
			return $this->property->getShopData($this->listing->getShopCode())->getUrlParam().'_'.$this->filter_min.'+'.$this->filter_max;
		} else {
			return '';
		}
	}


	public function getFilteredProductIds() : array
	{
		$ids = [];

		foreach( $this->data_map as $p_id=>$value ) {
			if($value>=$this->filter_min && $value<=$this->filter_max) {
				$ids[] = $p_id;
			}
		}

		return $ids;
	}

	public function renderFilterItem() : string
	{


		if( !$this->is_active ) {
			$filter_min = $this->min_value;
			$filter_max = $this->max_value;
		} else {
			$filter_min = $this->filter_min;
			$filter_max = $this->filter_max;
		}


		$view = $this->listing->getFilterView();

		$view->setVar('filter_property', $this);

		$view->setVar('min_value', floor($this->min_value) );
		$view->setVar('max_value', ceil($this->max_value) );

		$view->setVar('filter_min', $filter_min);
		$view->setVar('filter_max', $filter_max);


		return $view->render( 'parametrization/property/number/filter' );
	}


	public function renderSelectedFilterItem() : string
	{

		$view = $this->listing->getFilterView();

		$view->setVar('filter_property', $this);

		$view->setVar('filter_min', $this->filter_min);
		$view->setVar('filter_max', $this->filter_max);


		return $view->render( 'parametrization/property/number/filter_selected' );
	}

	public function disableNonRelevantFilters() : void
	{
		if($this->min_value==$this->max_value) {
			$this->setIsDisabled(true);
		}
	}

	public function resetCount() : void
	{
	}
}