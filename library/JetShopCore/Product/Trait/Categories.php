<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

trait Core_Product_Trait_Categories
{

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $main_category_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_type: false
	)]
	protected string $category_ids = '';

	/**
	 * @var Product_Category[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_Category::class,
		form_field_type: false
	)]
	protected array $categories = [];

	/**
	 * @var Category[]
	 */
	protected array|null $_categories = null;

	/**
	 * @return Category[]
	 */
	public function getCategories() : array
	{
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
		return isset($this->categories[$category_id]);
	}

	public function addCategory( int $category_id ) : bool
	{
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

		$this->categories[] = $_category;

		if(count($this->categories)==1) {
			$this->main_category_id = $category->getId();
		}

		Category::addSyncCategory( $category_id );


		return true;
	}

	public function removeCategory( int $category_id ) : bool
	{

		if(!isset($this->categories[$category_id])) {
			return false;
		}

		unset($this->categories[$category_id]);
		if( $this->_categories ) {
			unset($this->_categories[$category_id]);
		}

		if($category_id==$this->main_category_id) {
			$this->main_category_id = 0;
			foreach( $this->categories as $c ) {
				$this->main_category_id = $c->getCategoryId();
				break;
			}
		}

		Category::addSyncCategory( $category_id );

		return true;
	}

	public function setMainCategory( int $category_id ) : bool
	{

		if(!isset($this->categories[$category_id])) {
			return false;
		}

		$this->main_category_id = $category_id;

		Category::addSyncCategory( $category_id );

		return true;
	}

	public function getMainCategoryId() : int
	{
		return $this->main_category_id;
	}

	public function getMainCategory() : Category|null
	{
		if(!$this->main_category_id) {
			return null;
		}

		return Category::get($this->main_category_id);
	}

}