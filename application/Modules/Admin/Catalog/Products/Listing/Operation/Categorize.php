<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Http_Request;
use Jet\Tr;
use JetShop\Category;
use JetShop\Product;


class Listing_Operation_Categorize extends Listing_Operation
{
	
	protected ?Category $category = null;
	
	protected string $error_message = '';
	
	public function getKey(): string
	{
		return 'categorize';
	}
	
	public function getTitle(): string
	{
		return 'Categorize products';
	}
	
	public function isPrepared(): bool
	{
		$category_id = Http_Request::GET()->getInt('category_id');
		if($category_id) {
			$this->category = Category::get($category_id);
		}
		
		if(!$this->category) {
			$this->error_message = Tr::_('Please select category');
			
			return false;
		}
		
		if($this->category->getAutoAppendProducts()) {
			$this->error_message = Tr::_('Category has auto append mode. It is not possible to use such category for manual categorization.');
			
			return false;
		}
		
		return true;
	}
	
	public function getCategory(): ?Category
	{
		return $this->category;
	}
	
	public function getErrorMessage(): string
	{
		return $this->error_message;
	}
	
	
	
	/**
	 * @param Product[] $products
	 */
	public function perform( array $products ): void
	{
		$cat_id = $this->category->getId();
		
		foreach($products as $p) {
			if(!$p->hasCategory( $cat_id )) {
				$p->addCategory( $cat_id );
				$p->save();
			}
		}
		
		Category::syncCategories();
	}
	
}