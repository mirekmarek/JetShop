<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing_Filter;
use Jet\Form_Field_Hidden;
use Jet\Form;
use Jet\Http_Request;
use JetShop\Category;
use JetShop\Product_Category;

class Listing_Filter_Categories extends Data_Listing_Filter
{
	protected array $categories = [];
	
	
	public function catchGetParams(): void
	{
		$this->categoriesSet( Http_Request::GET()->getString('categories') );
	}
	
	public function generateFormFields( Form $form ): void
	{
		$categories = new Form_Field_Hidden('categories', '');
		$categories->setDefaultValue( $this->categories ? implode(',', $this->categories):'' );
		$form->addField($categories);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->categoriesSet( $form->field('categories')->getValue() );
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
	public function getSelected() : array
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

}