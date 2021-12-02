<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Tr;

/**
 *
 */
abstract class Core_ProductListing_Filter_Properties_Property_StencilOptions extends ProductListing_Filter_Properties_Property
{

	/**
	 * @var ProductListing_Filter_Properties_Property_Option[]
	 */
	protected array $filter_options = [];

	public function __construct( ProductListing $listing, Parametrization_Property $property )
	{
		parent::__construct( $listing, $property );

		$shop = $listing->getShop();

		foreach( $property->getOptions() as $option ) {
			if(!$option->getShopData($shop)->isActive()) {
				continue;
			}

			$f_o = new ProductListing_Filter_Properties_Property_Option( $this, $option );
			$this->filter_options[ $option->getId() ] = $f_o;
		}
	}

	/**
	 * @return ProductListing_Filter_Properties_Property_Option[]
	 */
	public function getFilterOptions() : array
	{
		return $this->filter_options;
	}

	/**
	 * @return ProductListing_Filter_Properties_Property_Option[]
	 */
	public function getActiveFilterOptions() : array
	{
		$options = [];

		foreach($this->filter_options as $option) {
			if($option->isActive()) {
				$options[$option->getOption()->getId()] = $option;
			}
		}

		return $options;
	}


	public function getTargetFilterEditForm( Form $form, array $target_filter ) : void
	{
		$p_id = $this->property->getId();

		if(!isset($target_filter['properties'])) {
			$target_filter['properties'] = [];
		}

		if(!isset($target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id] = [
				'options' => [],
				'disable_filter' => false
			];
		}

		if(!array_key_exists('disable_filter', $target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id]['disable_filter'] = false;
		}
		if(!array_key_exists('options', $target_filter['properties'][$p_id])) {
			$target_filter['properties'][$p_id]['options'] = [];
		}

		$form->addField(new Form_Field_Checkbox('/properties/'.$p_id.'/disable_filter', Tr::_('Disable filter'), $target_filter['properties'][$p_id]['disable_filter']));

		foreach($this->property->getOptions() as $option) {
			$o_id = $option->getId();
			if(!isset($target_filter['properties'][$p_id]['options'][$o_id])) {
				$target_filter['properties'][$p_id]['options'][$o_id] = false;
			}

			$form->addField(new Form_Field_Checkbox('/properties/'.$p_id.'/option_'.$o_id, $option->getShopData()->getFilterLabel(), $target_filter['properties'][$p_id]['options'][$o_id]));

		}

	}

	public function catchTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$p_id = $this->property->getId();

		if($form->field('/properties/'.$p_id.'/disable_filter')->getValue()) {
			$disable_filter = true;
		} else {
			$disable_filter = false;
		}

		$options = [];

		foreach($this->property->getOptions() as $option) {
			$o_id = $option->getId();

			if($form->field('/properties/'.$p_id.'/option_'.$o_id)->getValue()) {
				$options[$o_id] = true;
			}

		}

		if(
			$disable_filter ||
			$options
		) {
			$target_filter['properties'][$p_id] = [
				'disable_filter' => $disable_filter,
				'options' => $options
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

		if(!empty($fd['options'])) {
			foreach(array_keys($fd['options']) as $o_id) {
				if(isset($this->filter_options[$o_id])) {
					$this->filter_options[$o_id]->setIsForced(true);
				}
			}

		}
	}

	public function getStateData() : array
	{
		$options = [];

		foreach( $this->filter_options as $id=>$filter ) {
			if($filter->isActive()) {
				$options[] = $id;
			}
		}

		return [
			'is_active' => $this->isActive(),
			'options' => $options
		];
	}

	public function initByStateData( array $state_data ) : void
	{
		if($state_data['is_active']) {
			$options = $state_data['options'];

			foreach( $this->filter_options as $id=>$filter ) {
				$filter->setIsActive( in_array($id, $options) );
			}
		} else {
			foreach( $this->filter_options as $filter ) {
				$filter->setIsActive( false );
			}

		}
	}

	public function generateCategoryTargetUrlPart() : string
	{
		$shop = $this->listing->getShop();

		$options = [];
		foreach($this->filter_options as $o) {
			if($o->isForced()) {
				$options[] = $o->getOption()->getShopData($shop)->getUrlParam();
			}
		}
		if(!$options) {
			return '';
		}

		return $this->property->getShopData($shop)->getUrlParam().'_'.implode('+', $options);
	}

	public function generateUrlPart() : string
	{
		$shop = $this->listing->getShop();

		$options = [];
		foreach($this->filter_options as $o) {
			if($o->isActive()) {
				$options[] = $o->getOption()->getShopData($shop)->getUrlParam();
			}
		}
		if(!$options) {
			return '';
		}

		return $this->property->getShopData($shop)->getUrlParam().'_'.implode('+', $options);
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		$shop = $this->listing->getShop();
		$prefix = $this->property->getShopData($shop)->getUrlParam().'_';

		foreach($parts as $i=>$part) {
			if(stripos($part, $prefix)===0) {
				unset($parts[$i]);

				$options = explode('_', $part)[1];
				$options = explode('+', $options);

				foreach( $this->filter_options as $filter_option ) {

					$url_param = $filter_option->getOption()->getShopData($shop)->getUrlParam();

					if(in_array($url_param, $options)) {
						if(!$filter_option->isForced()) {
							$filter_option->setIsActive(true);
						}
					}
				}

			}
		}
	}

	public function prepareFilter( array $data_map ) : void
	{
		$map = [];

		foreach($data_map as $p_id=>$_option_ids) {
			foreach($_option_ids as $option_ids) {
				$option_ids = explode(',', $option_ids);

				foreach($option_ids as $o_id) {
					$o_id = (int)$o_id;

					if(!isset($map[$o_id])) {
						$map[$o_id] = [];
					}

					$map[$o_id][$p_id] = true;
				}
			}
		}

		foreach($map as $option_id=>$product_ids) {
			if(isset($this->filter_options[$option_id])) {
				$this->filter_options[$option_id]->setProductIds( array_keys($product_ids) );
			}
		}
	}

	public function generateFilterUrlPart() : string
	{
		$shop = $this->listing->getShop();

		$options = [];
		foreach($this->filter_options as $o) {
			if($o->isActive()) {
				$options[] = $o->getOption()->getShopData($shop)->getUrlParam();
			}
		}
		if(!$options) {
			return '';
		}

		return $this->property->getShopData($shop)->getUrlParam().'_'.implode('+', $options);
	}

	public function isForced() : bool
	{
		foreach( $this->filter_options as $o ) {
			if($o->isForced()) {
				return true;
			}
		}

		return false;
	}

	public function isActive() : bool
	{
		foreach( $this->filter_options as $o ) {
			if($o->isActive()) {
				return true;
			}
		}

		return false;
	}

	public function getFilteredProductIds() : array
	{
		$id_map = [];

		foreach($this->filter_options as $o) {
			if($o->isActive() || $o->isForced() ) {
				$id_map[] = $o->getProductIds();
			}
		}

		return $this->listing->idMapIntersect( $id_map );
	}

	public function renderFilterItem() : string
	{
		$view = $this->listing->getFilterView();

		$view->setVar('filter_property', $this);

		return $view->render( 'parametrization/property/options/filter' );
	}

	public function renderSelectedFilterItem() : string
	{
		$view = $this->listing->getFilterView();

		$view->setVar('filter_property', $this);

		return $view->render( 'parametrization/property/options/filter_selected' );
	}

	public function disableNonRelevantFilters() : void
	{
		$enabled_count = 0;
		foreach($this->filter_options as $option) {
			if(!$option->getCount()) {
				$option->setIsDisabled(true);
			} else {
				$enabled_count++;
			}
		}

		if($enabled_count<2) {
			$this->setIsDisabled(true);
		}
	}

	public function resetCount() : void
	{
		foreach($this->filter_options as $option) {
			$option->resetCount();
		}
	}

}