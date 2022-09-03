<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Tr;

abstract class Core_ProductListing_Sort {
	const CACHE_KEY = 'sort';

	protected ProductListing $listing;
	protected Shops_Shop $shop;

	/**
	 * @var ProductListing_Sort_Option[]
	 */
	protected array|null $sort_options = null;

	public function __construct( ProductListing $listing )
	{
		$this->listing = $listing;
		$this->shop = $listing->getShop();

		$this->init();

		$has_some_active = false;

		foreach($this->sort_options as $so) {
			if( $so->isForced() ) {
				$so->setIsActive(true);
				$has_some_active = true;
			}
		}

		if(!$has_some_active) {
			foreach($this->sort_options as $so) {
				if( $so->isDefault() ) {
					$so->setIsActive(true);
					$has_some_active = true;
				}
			}
		}

		if(!$has_some_active) {
			foreach($this->sort_options as $so) {
				$so->setIsActive(true);

				break;
			}
		}
	}

	abstract protected function init() : void;

	abstract public function prepare( array $initial_product_ids ) : void;

	abstract public static function getTargetFilterEditForm_SortOptionsScope() : array;

	abstract public function getSortUrlParam() : string;

	public function getTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$selected = '';
		if(!empty($target_filter['sort_order'])) {
			$selected = $target_filter['sort_order'];
		}

		$sort = new Form_Field_Select('sort_order', Tr::_('Sort order: ', [], Category::getManageModuleName()) );
		$sort->setDefaultValue( $selected );

		$select_options = [
			'' => Tr::_('- default -', [], Category::getManageModuleName())
		];

		foreach( ProductListing_Sort::getTargetFilterEditForm_SortOptionsScope() as $key=>$name ) {
			$select_options[$key] = $name;
		}

		$sort->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => Tr::_('Please select sort option', [], Category::getManageModuleName())
		]);

		$sort->setSelectOptions( $select_options );

		$form->addField($sort);
	}

	public function catchTargetFilterEditForm( Form $form, &$target_filter ) : void
	{
		$target_filter['sort_order'] = $form->getField('sort_order')->getValue();
	}

	public function initByTargetFilter( array &$target_filter ) : void
	{

		if(
			isset($target_filter['sort_order']) &&
			isset($this->sort_options[$target_filter['sort_order']])
		) {
			$this->sort_options[$target_filter['sort_order']]->setIsForced(true);
		}
	}

	public function setSelectedSort( string $option_id ) : void
	{
		if(!isset($this->sort_options[$option_id])) {
			return;
		}

		foreach($this->sort_options as $id=>$option) {
			$option->setIsActive( ($id==$option_id) );
		}
	}

	public function getStateData( array &$state_data ) : void
	{
		$selected = '';

		foreach($this->sort_options as $option) {
			if($option->isActive()) {
				$selected = $option->getId();

				break;
			}
		}

		$state_data['selected_sort'] = $selected;

	}

	public function initByStateData( array $state_data ) : void
	{
		if(isset($state_data['selected_sort'])) {
			$this->setSelectedSort( $state_data['selected_sort'] );
		}
	}

	public function generateCategoryTargetUrl( array &$parts ) : void
	{
		foreach($this->sort_options as $sort_option) {
			if( $sort_option->isForced() ) {
				if(!$sort_option->isDefault()) {
					$parts[] = $this->getSortUrlParam().'_'.$sort_option->getUrlParam();
				}

				break;
			}
		}
	}

	public function generateUrl( array &$parts ) : void
	{
		foreach($this->sort_options as $sort_option) {
			if( $sort_option->isActive() ) {
				if(!$sort_option->isDefault()) {
					$parts[] = $this->getSortUrlParam().'_'.$sort_option->getUrlParam();
				}

				break;
			}
		}
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		$prefix = $this->getSortUrlParam().'_';

		foreach($parts as $i=>$part) {
			if(stripos($part, $prefix)===0) {
				unset($parts[$i]);

				$sort_param = explode('_', $part)[1];


				foreach( $this->sort_options as $sort_option ) {

					$is_active = false;
					if($sort_option->getUrlParam()==$sort_param) {
						if(!$sort_option->isForced()) {
							$is_active = true;
						}
					}

					$sort_option->setIsActive($is_active);
				}

			}
		}
	}

	public function sort( array $filtered_product_ids ) : array
	{
		foreach($this->sort_options as $sort_option) {
			if( $sort_option->isActive() ) {
				$sorter = $sort_option->getSorter();

				return $sorter( $filtered_product_ids );
			}
		}

		return $filtered_product_ids;
	}

	/**
	 * @return ProductListing_Sort_Option[]
	 */
	public function getSortOptions() : array
	{
		return $this->sort_options;
	}

}