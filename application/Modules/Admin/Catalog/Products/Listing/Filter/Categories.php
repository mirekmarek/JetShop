<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Http_Request;
use JetApplication\Category_Product;

class Listing_Filter_Categories extends DataListing_Filter
{
	public const KEY = 'categories';
	
	protected array $categories = [];
	
	protected ?string $mode = null;
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->categoriesSet( Http_Request::GET()->getString('categories') );
	}
	
	public function generateFormFields( Form $form ): void
	{
		$categories = new Form_Field_Hidden('categories');
		$categories->setDefaultValue( $this->getSelectedCategoryIds(true) );
		
		$form->addField( $categories );
	}
	
	public function catchForm( Form $form ): void
	{
		$this->categoriesSet( $form->field('categories')->getValue() );
	}
	
	public function generateWhere(): void
	{
		if($this->categories) {
			
			$ids = Category_Product::dataFetchCol(
				select: ['product_id'],
				where: [
						'category_id' => $this->categories
					]
			);
			
			if(!$ids) {
				$ids = [0];
			}
			
			
			$this->listing->addFilterWhere([
				'id'   => $ids,
			]);
		}
	}
	
	
	public function getSelectedCategoryIds( bool $as_string=false ) : array|string
	{
		return $as_string ? implode(',', $this->categories) : $this->categories;
	}
	
	protected function categoriesSet( string $categories ) : void
	{
		if($categories) {
			$this->categories = explode(',', $categories);
			foreach($this->categories as $i=>$id) {
				$this->categories[$i] = (int)$id;
			}
			
			$this->categories = array_unique($this->categories);
			
			$this->listing->setParam('categories', implode(',', $this->categories));
		} else {
			$this->categories = [];
			$this->listing->setParam('categories', '');
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