<?php
namespace JetShop;

use JetApplication\ProductFilter_Filter_PropertyOptions;
use JetApplication\ProductFilter_Filter_PropertyOptions_Option;

abstract class Core_ProductFilter_Filter_PropertyOptions_Property
{
	protected ProductFilter_Filter_PropertyOptions $filter;
	
	protected int $property_id;
	
	protected bool $has_selected_option = false;
	
	protected array $product_ids = [];
	
	/**
	 * @var ProductFilter_Filter_PropertyOptions_Option[]
	 */
	protected array $options = [];
	
	
	public function __construct( ProductFilter_Filter_PropertyOptions $filter, int $property_id, array $option_ids )
	{
		$this->filter = $filter;
		$this->property_id = $property_id;
		
		foreach($option_ids as $option_id) {
			$this->options[$option_id] = new ProductFilter_Filter_PropertyOptions_Option( $this->filter, $option_id );
		}
	}
	
	public function setSelectedOptions( array $selected_option_ids ) : void
	{
		foreach($selected_option_ids as $selected_option_id) {
			if(isset($this->options[$selected_option_id])) {
				$this->has_selected_option = true;
				$this->options[$selected_option_id]->setSelected( true );
			}
		}
	}
	
	public function selectOption( int $option_id ) : void
	{
		if(!isset($this->options[$option_id])) {
			$this->options[$option_id] = new ProductFilter_Filter_PropertyOptions_Option( $this->filter, $option_id );
		}
		
		$this->has_selected_option = true;
		$this->options[$option_id]->setSelected( true );
	}
	
	
	public function unselectOption( int $option_id ) : void
	{
		if(isset($this->options[$option_id])) {
			$this->options[$option_id]->setSelected( false );
		}
		
		$this->has_selected_option = false;
		foreach($this->options as $option) {
			if($option->getSelected()) {
				$this->has_selected_option = true;
			}
		}
	}
	
	
	public function getSelectedOptionIds() : array
	{
		$selected = [];
		
		foreach($this->options as $option) {
			if($option->getSelected()) {
				$selected[] = $option->getOptionId();
			}
		}
		
		return $selected;
	}
	
	public function getOption( int $option_id ) : ?ProductFilter_Filter_PropertyOptions_Option
	{
		return $this->options[ $option_id ]??null;
	}
	
	
	public function addProductId( int $option_id, int $product_id ) : void
	{
		if(isset($this->options[$option_id])) {
			$this->product_ids[] = $product_id;
			$this->options[$option_id]->addProductId( $product_id );
		}
	}
	
	public function getId(): int
	{
		return $this->property_id;
	}
	

	public function getProductIds(): array
	{
		return $this->product_ids;
	}
	

	public function hasSelectedOption(): bool
	{
		return $this->has_selected_option;
	}
	
	
	
	public function getFilteredProductIds() : array
	{
		if(!$this->has_selected_option) {
			return $this->product_ids;
		}
		
		$ids = [];
		
		foreach($this->options as $option) {
			if($option->getSelected()) {
				$ids = array_merge( $ids, $option->getProductIds() );
			}
		}
		
		return array_unique( $ids );
	}
	
	
}