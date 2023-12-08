<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing_Operation;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Category;


class Listing_Operation_Categorize extends DataListing_Operation
{
	public const KEY = 'categorize';
	
	protected ?Category $category = null;
	
	protected string $error_message = '';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Categorize products');
	}
	
	public function getOperationGetParams(): array
	{
		return ['category_id'];
	}
	
	public function init( array $products ): void
	{
		$category_id = Http_Request::GET()->getInt('category_id');
		if($category_id) {
			$this->category = Category::get($category_id);
		}
		
	}
	
	public function isPrepared(): bool
	{
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
	
	
	
	public function perform(): void
	{
		$cat_id = $this->category->getId();
		
		foreach($this->products as $p) {
			if(!$p->hasCategory( $cat_id )) {
				$p->addCategory( $cat_id );
			}
		}
		
	}
}