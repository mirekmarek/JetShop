<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing_Operation;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Category;


class Listing_Operation_Uncategorize extends DataListing_Operation
{
	public const KEY = 'uncategorize';
	
	/**
	 * @var Category[]
	 */
	protected array $categories;
	
	/**
	 * @var Category[]
	 */
	protected array $selected_categories;
	
	protected string $error_message = '';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Uncategorize products');
	}
	
	public function getOperationGetParams(): array
	{
		return ['category_id'];
	}
	
	public function init( array $products ): void
	{
		$this->categories = [];
		foreach($this->products as $product) {
			foreach($product->getCategories() as $category) {
				if(!$category->getAutoAppendProducts()) {
					$this->categories[$category->getId()] = $category;
				}
			}
		}
		
		uasort($this->categories, function( Category $a, Category $b ) {
			return strcmp( $a->getPathName(), $b->getPathName() );
		});
		
		$this->selected_categories = [];
		$selected_categories = Http_Request::GET()->getRaw('category_id');
		
		if(is_array($selected_categories)) {
			foreach($selected_categories as $id) {
				if(isset($this->categories[$id])) {
					$this->selected_categories[$id] = $this->categories[$id];
				}
			}
		}
	}
	
	public function isPrepared(): bool
	{
		if(!$this->getSelectedCategories()) {
			$this->error_message = Tr::_('Please select some category');
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * @return Category[]
	 */
	public function getCategories(): array
	{
		return $this->categories;
	}
	
	/**
	 * @return Category[]
	 */
	public function getSelectedCategories() : array
	{
		return $this->selected_categories;
	}
	
	public function getErrorMessage(): string
	{
		return $this->error_message;
	}
	
	
	
	public function perform(): void
	{
		
		foreach($this->products as $p) {
			foreach($this->getSelectedCategories() as $cat) {
				$p->removeCategory($cat->getId());
			}
		}
	}
	
}