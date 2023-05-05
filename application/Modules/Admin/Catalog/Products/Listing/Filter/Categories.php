<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use Jet\Http_Request;
use JetApplication\Category;
use JetApplication\Product_Category;

class Listing_Filter_Categories extends Listing_Filter
{
	
	const MODE_TREE = 'tree';
	const MODE_MULTIPLE = 'multiple';
	
	const DEFAULT_MODE = self::MODE_TREE;
	
	protected array $categories = [];
	
	protected ?string $mode = null;
	
	public function getKey(): string
	{
		return static::CATEGORIES;
	}
	
	public function catchGetParams(): void
	{
		$this->categoriesSet( Http_Request::GET()->getString('categories') );
		$this->getMode();
	}
	
	public function generateFormFields( Form $form ): void
	{
	}
	
	public function catchForm( Form $form ): void
	{
	}
	
	public function generateWhere(): void
	{
		if($this->categories) {
			
			$items = Product_Category::fetch([
				'product_category' => [
					'category_id' => $this->categories
				]
			]);
			
			$ids = [];
			
			foreach($items as $item) {
				$ids[] = $item->getProductId();
			}
			
			if(!$ids) {
				$ids = [0];
			}
			
			
			$this->listing->addWhere([
				'id'   => $ids,
			]);
		}
	}
	
	
	
	/**
	 * @return Category[]
	 */
	public function getSelectedCategories() : array
	{
		if(!$this->categories) {
			return [];
		}
		
		$res = [];
		
		foreach($this->categories as $id) {
			$category = Category::get($id);
			if($category) {
				$res[$id] = $category;
			}
		}
		
		return $res;
		
	}
	
	public function getSelectedCategoryIds( bool $as_string ) : array|string
	{
		return $as_string ? implode(',', $this->categories) : $this->categories;
	}
	
	protected function categoriesSet( string $categories )
	{
		if($categories) {
			$this->categories = explode(',', $categories);
			foreach($this->categories as $i=>$id) {
				$this->categories[$i] = (int)$id;
			}
			
			$this->listing->setGetParam('categories', implode(',', $this->categories));
		} else {
			$this->categories = [];
			$this->listing->setGetParam('categories', '');
		}
		
	}
	
	public function getCurrentCategoryId() : int
	{
		if(!$this->categories) {
			return 0;
		}
		
		return $this->categories[0];
	}
	
	public function getCategoryUrl( int $id ) : string
	{
		return Http_Request::currentURI(
			set_GET_params: ['categories'=>$id],
			unset_GET_params: ['id']
		);
	}
	
	public function getMode() : string
	{
		if($this->mode===null) {
			$this->mode = Http_Request::GET()->getString(
				key: 'category_mode',
				default_value: static::DEFAULT_MODE,
				valid_values: [
					static::MODE_TREE,
					static::MODE_MULTIPLE
				]
			);
			
			if($this->mode==static::DEFAULT_MODE) {
				$this->listing->unsetGetParam('category_mode');
			} else {
				$this->listing->setGetParam('category_mode', $this->mode);
			}
			
		}
		
		return $this->mode;
	}
	
	public function getModeUrl( string $mode ) : string
	{
		return Http_Request::currentURI(['category_mode'=>$mode]);
	}
	
}