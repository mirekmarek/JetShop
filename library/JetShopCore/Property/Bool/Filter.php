<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Checkbox;

/**
 *
 */
abstract class Core_Property_Bool_Filter extends Property_Filter
{

	protected array $filtered_ids = [];

	protected ?int $_count = null;

	public function getAutoAppendProductFilterEditForm( Form $form ) : void
	{
		$p_id = $this->property->getId();
		
		$selected = new Form_Field_Checkbox('/properties/'.$p_id.'/selected', $this->property->getShopData()->getLabel() );
		$selected->setDefaultValue( $this->isActive() );
		
		$form->addField( $selected );

	}

	public function catchAutoAppendFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$p_id = $this->property->getId();

		$selected = $form->field('/properties/'.$p_id.'/selected')->getValue();

		if($selected) {
			$target_filter['properties'][$p_id] = [
				'selected' => $selected
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

		if(!empty($fd['selected'])) {
			$this->setIsActive( true );
		}
	}

	public function getStateData() : array
	{
		return [
			'is_active' => $this->isActive()
		];
	}

	public function initByStateData( array $state_data ) : void
	{
		$this->setIsActive( $state_data['is_active'] );
	}

	/**
	 *
	 * @return string
	 */
	public function generateUrlPart() : string
	{
		if($this->isActive()) {
			return $this->property->getShopData($this->listing->getShop())->getUrlParam().'_1';
		} else {
			return '';
		}
	}

	public function prepareFilter( array $data_map ) : void
	{
		foreach($data_map as $p_id=>$d) {
			if(!empty($d[0])) {
				$this->filtered_ids[] = $p_id;
			}
		}

	}

	public function generateFilterUrlPart() : string
	{
		if($this->isActive()) {
			return $this->property->getShopData($this->listing->getShop())->getUrlParam().'_1';
		} else {
			return '';
		}
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		$prefix = $this->property->getShopData($this->listing->getShop())->getUrlParam().'_1';

		foreach($parts as $i=>$part) {
			if($part==$prefix) {
				unset($parts[$i]);
				$this->setIsActive(true);
			}
		}

	}

	public function getFilteredProductIds() : array
	{
		return $this->filtered_ids;
	}

	public function renderFilterItem() : string
	{
		$view = $this->listing->getFilterView();

		$view->setVar('filter_property', $this);

		return $view->render( 'parametrization/property/bool/filter' );
	}

	public function renderSelectedFilterItem() : string
	{
		$view = $this->listing->getFilterView();

		$view->setVar('filter_property', $this);

		return $view->render( 'parametrization/property/bool/filter_selected' );
	}

	public function getCount() : int
	{
		if($this->_count===null) {
			$property_id = $this->getProperty()->getId();

			$ids = $this->listing->properties()->internalGetFilteredProductIdsWithoutProperty(
				$this->filtered_ids,
				$property_id
			);


			$this->_count = count($ids);

		}

		return $this->_count;
	}


	public function resetCount() : void
	{
		$this->_count = null;
	}


	public function generateURLWithout() : string
	{
		$active = $this->is_active;

		$this->is_active = false;

		$url = $this->listing->generateUrl();

		$this->is_active = $active;

		return $url;
	}

	public function generateURLWith() : string
	{
		$active = $this->is_active;

		$this->is_active = true;

		$url = $this->listing->generateUrl();

		$this->is_active = $active;

		return $url;
	}

	public function disableNonRelevantFilters() : void
	{
		if(count($this->filtered_ids)) {
			$this->setIsDisabled(true);
		}
	}

}