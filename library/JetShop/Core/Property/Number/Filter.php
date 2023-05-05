<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Float;

use JetApplication\Property_Filter;

/**
 *
 */
abstract class Core_Property_Number_Filter extends Property_Filter
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

	public function getAutoAppendProductFilterEditForm( Form $form ) : void
	{
		$p_id = $this->property->getId();

		
		$min = new Form_Field_Float('/properties/'.$p_id.'/min', 'minimum:' );
		$max = new Form_Field_Float('/properties/'.$p_id.'/max', 'maximum:' );
		
		if($this->isActive()) {
			$min->setDefaultValue( $this->filter_min );
			$max->setDefaultValue( $this->filter_max );
		}
		
		$form->addField($min);
		$form->addField($max);

	}

	public function catchAutoAppendFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$p_id = $this->property->getId();

		$min = $form->field('/properties/'.$p_id.'/min')->getValue();
		$max = $form->field('/properties/'.$p_id.'/max')->getValue();

		if($min!=0 || $max!=0) {
			$target_filter['properties'][$p_id] = [
				'min' => $min,
				'max' => $max
			];

		}
	}

	public function initAutoAppendFilter( array $target_filter ) : void
	{
		$p_id = $this->property->getId();

		if(!isset($target_filter['properties'][$p_id])) {
			return;
		}

		$fd = $target_filter['properties'][$p_id];

		if(
			!empty($fd['min']) ||
			!empty($fd['max'])
		) {
			$this->activateFilter((float)$fd['min'], (float)$fd['max']);
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


	public function generateUrlPart() : string
	{
		if($this->isActive()) {
			return $this->property->getShopData($this->listing->getShop())->getUrlParam().'_'.$this->filter_min.'+'.$this->filter_max;
		} else {
			return '';
		}
	}


	public function parseFilterUrl( array &$parts ) : void
	{
		$shop = $this->listing->getShop();
		$prefix = $this->property->getShopData($shop)->getUrlParam().'_';

		foreach($parts as $i=>$part) {
			if(stripos($part, $prefix)===0) {
				unset($parts[$i]);

				$min_max = explode('_', $part)[1];
				$min_max = explode('+', $min_max);

				$this->setIsActive(true);

				$this->filter_min = $min_max[0];
				$this->filter_max = $min_max[1];

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
			return $this->property->getShopData($this->listing->getShop())->getUrlParam().'_'.$this->filter_min.'+'.$this->filter_max;
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