<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Product_Category;
use JetApplication\Category;
use JetApplication\Product;

trait Core_Product_Trait_Categories
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $category_ids = '';

	/**
	 * @var Product_Category[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_Category::class,
	)]
	protected array $categories = [];

	/**
	 * @var Category[]
	 */
	protected array|null $_categories = null;
	
	/**
	 * @return Product_Category[]
	 */
	public function getCategoriesAssoc() : array
	{
		if($this->type==Product::PRODUCT_TYPE_VARIANT) {
			return $this->getVariantMasterProduct()->getCategoriesAssoc();
		}
		
		return $this->categories;
	}

	/**
	 * @return Category[]
	 */
	public function getCategories() : array
	{
		if($this->type==Product::PRODUCT_TYPE_VARIANT) {
			return $this->getVariantMasterProduct()->getCategories();
		}
		
		if($this->_categories===null) {
			$this->_categories = [];
			foreach($this->categories as $c) {
				$category = Category::get($c->getCategoryId());
				if($category) {
					$this->_categories[ $category->getId() ] = $category;
				}
			}
		}

		return $this->_categories;
	}

	public function hasCategory( int $category_id ) : bool
	{
		if($this->type==Product::PRODUCT_TYPE_VARIANT) {
			return $this->getVariantMasterProduct()->hasCategory( $category_id );
		}
		
		return isset($this->categories[$category_id]);
	}

	public function addCategory( int $category_id, bool $auto_appended=false ) : bool
	{
		if($this->type==Product::PRODUCT_TYPE_VARIANT) {
			return false;
		}
		
		$category = Category::get( $category_id );
		if(!$category) {
			return false;
		}

		if(isset($this->categories[$category->getId()])) {
			return false;
		}

		$_category = new Product_Category();
		$_category->setProductId( $this->id );
		$_category->setCategoryId( $category->getId() );
		$_category->setAutoAppended( $auto_appended );
		$_category->save();

		$this->categories[] = $_category;
		
		Category::addSyncCategory( $category_id );


		return true;
	}

	public function removeCategory( int $category_id, bool $force=false ) : bool
	{
		if($this->type==Product::PRODUCT_TYPE_VARIANT) {
			return false;
		}

		if(!isset($this->categories[$category_id])) {
			return false;
		}
		
		if(
			$this->categories[$category_id]->getAutoAppended() &&
			!$force
		) {
			return false;
		}
		
		$this->categories[$category_id]->delete();
		unset($this->categories[$category_id]);
		if( $this->_categories ) {
			unset($this->_categories[$category_id]);
		}
		
		Category::addSyncCategory( $category_id );

		return true;
	}
	
}