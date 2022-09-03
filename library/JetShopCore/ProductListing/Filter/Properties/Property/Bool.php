<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Tr;

/**
 *
 */
abstract class Core_ProductListing_Filter_Properties_Property_Bool extends ProductListing_Filter_Properties_Property
{

	protected array $filtered_ids = [];

	protected ?int $_count = null;

	public function getTargetFilterEditForm( Form $form, array $target_filter ) : void
	{
		$p_id = $this->property->getId();

		if(!isset($target_filter['properties'])) {
			$target_filter['properties'] = [];
		}

		if(!isset($target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id] = [
				'selected' => false,
				'disable_filter' => false
			];
		}

		if(!array_key_exists('disable_filter', $target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id]['disable_filter'] = false;
		}
		if(!array_key_exists('selected', $target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id]['selected'] = false;
		}

		$disable_filter = new Form_Field_Checkbox('/properties/'.$p_id.'/disable_filter', Tr::_('Disable filter'));
		$disable_filter->setDefaultValue( $target_filter['properties'][$p_id]['disable_filter'] );
		$selected = new Form_Field_Checkbox('/properties/'.$p_id.'/selected', $this->property->getShopData()->getLabel() );
		$selected->setDefaultValue( $target_filter['properties'][$p_id]['selected'] );


		$form->addField( $disable_filter );
		$form->addField( $selected );

	}

	public function catchTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$p_id = $this->property->getId();

		$selected = $form->field('/properties/'.$p_id.'/selected')->getValue();
		$disable_filter = $form->field('/properties/'.$p_id.'/disable_filter')->getValue();

		if($selected  || $disable_filter) {
			$target_filter['properties'][$p_id] = [
				'disable_filter' => $disable_filter,
				'selected' => $selected
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

		if(!empty($fd['selected'])) {
			$this->setIsActive( true );
			$this->setIsForced( true );
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

	public function generateCategoryTargetUrlPart() : string
	{
		if($this->isForced()) {
			return $this->property->getShopData($this->listing->getShop())->getUrlParam().'_1';
		} else {
			return '';
		}
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

				if(!$this->isForced()) {
					$this->setIsActive(true);
				}
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

			$ids = $this->listing->params()->internalGetFilteredProductIdsWithoutProperty(
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