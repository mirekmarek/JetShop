<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\Form;
use Jet\Tr;

/**
 *
 */
#[DataModel_Definition(
	name: 'categories_shop_data',
	database_table_name: 'categories_shop_data',
	parent_model_class: Category::class,
)]
abstract class Core_Category_ShopData extends CommonEntity_ShopData implements Images_ShopDataInterface {

	use Images_ShopDataTrait;

	const IMG_MAIN = 'main';
	const IMG_PICTOGRAM = 'pictogram';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to:  'main.id',
		form_field_type:  false
	)]
	protected int $category_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label:  'Name:'
	)]
	protected string $name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label:  'Alternative name:'
	)]
	protected string $second_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_type:  Form::TYPE_WYSIWYG,
		max_len: 65536,
		form_field_label:  'Description:'
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_label:  'H1:'
	)]
	protected string $seo_h1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_label:  'Title:'
	)]
	protected string $seo_title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_label:  'Description:'
	)]
	protected string $seo_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_label:  'Keywords:'
	)]
	protected string $seo_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label:  'Disable canonical URL'
	)]
	protected bool $seo_disable_canonical = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
		form_field_type:  false
	)]
	protected string $URL_path_part = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
		form_field_label:  'Keywords words for internal fulltext:'
	)]
	protected string $internal_fulltext_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type:  false
	)]
	protected string $image_main = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type:  false
	)]
	protected string $image_pictogram = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999999,
		form_field_type: false
	)]
	protected string $product_ids = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_type:  false
	)]
	protected int $visible_products_count = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_type:  false
	)]
	protected int $nested_visible_products_count = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_type:  false
	)]
	protected string $path = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_type:  false
	)]
	protected string $children = '';

	/**
	 * @var Category[]|DataModel_Fetch_Instances
	 */
	protected DataModel_Fetch_Instances|null|array $_children = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_type:  false
	)]
	protected string $all_children = '';


	protected function _getProperty( string $property ) : mixed
	{
		$category = Category::get($this->category_id);

		if(
			!$this->{$property} &&
			(
				$category->getType()==Category::CATEGORY_TYPE_LINK ||
				$category->getType()==Category::CATEGORY_TYPE_VIRTUAL
			)
		) {
			$target = $category->getTargetCategory();
			if($target) {
				$shop_data = $target->getShopData( $this->getShop() );

				return $shop_data->{$property};
			}
		}

		return $this->{$property};

	}

	public function getVisibleProductsCount() : int
	{
		return $this->visible_products_count;
	}

	public function getNestedVisibleProductsCount() : int
	{
		return $this->nested_visible_products_count;
	}

	public function getName() : string
	{
		return $this->_getProperty( 'name' );
	}

	public function setName( string $name ) : void
	{
		$this->name = $name;
		$this->generateURLPathPart();
	}

	public function getSecondName() : string
	{
		return $this->_getProperty( 'second_name' );
	}

	public function setSecondName( string $second_name ) : void
	{
		$this->second_name = $second_name;
	}

	public function getDescription() : string
	{
		return $this->_getProperty( 'description' );
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getSeoH1() : string
	{
		return $this->_getProperty( 'seo_h1' );
	}

	public function setSeoH1( string $seo_h1 ) : void
	{
		$this->seo_h1 = $seo_h1;
	}

	public function getSeoTitle() : string
	{
		return $this->_getProperty( 'seo_title' );
	}

	public function setSeoTitle( string $seo_title ) : void
	{
		$this->seo_title = $seo_title;
	}

	public function getSeoDescription() : string
	{
		return $this->_getProperty( 'seo_description' );
	}

	public function setSeoDescription( string $seo_description ) : void
	{
		$this->seo_description = $seo_description;
	}

	public function getSeoKeywords() : string
	{
		return $this->_getProperty( 'seo_keywords' );
	}

	public function setSeoKeywords( string $seo_keywords ) : void
	{
		$this->seo_keywords = $seo_keywords;
	}

	public function isSeoDisableCanonical() : bool
	{
		return $this->seo_disable_canonical;
	}

	public function setSeoDisableCanonical( bool $seo_disable_canonical ) : void
	{
		$this->seo_disable_canonical = $seo_disable_canonical;
	}

	public function getInternalFulltextKeywords() : string
	{
		return $this->internal_fulltext_keywords;
	}

	public function setInternalFulltextKeywords( string $internal_fulltext_keywords ) : void
	{
		$this->internal_fulltext_keywords = $internal_fulltext_keywords;
	}

	public function getURLPathPart() : string
	{
		return $this->URL_path_part;
	}

	public function setURLPathPart( string $URL_path_part, bool $save=false ) : void
	{
		$this->URL_path_part = $URL_path_part;
		if($save) {
			$this->updateData(
				[
					'URL_path_part'=>$URL_path_part
				],
				[
					'category_id' => $this->category_id,
					'AND',
					$this->getShop()->getWhere()
				]
			);
		}
	}

	public function getURL() : string
	{
		return Shops::getURL( $this->getShop(), [$this->URL_path_part] );
	}

	public function generateURLPathPart() : void
	{
		if(!$this->category_id) {
			return;
		}

		$this->URL_path_part = Shops::generateURLPathPart( $this->name, 'c', $this->category_id, $this->getShop() );
	}

	public function getImageEntity() : string
	{
		return 'category';
	}

	public function getImageObjectId() : int|string
	{
		return $this->category_id;
	}

	public static function getImageClasses() : array
	{
		return [
			Category_ShopData::IMG_MAIN => Tr::_('Main image', [], Category::getManageModuleName() ),
			Category_ShopData::IMG_PICTOGRAM => Tr::_('Pictogram image', [], Category::getManageModuleName() ),
		];
	}

	public function getImage( string $image_class ) : string
	{
		return $this->_getProperty('image_'.$image_class);
	}

	public function setImageMain( string $image_main ) : void
	{
		$this->setImage( Category_ShopData::IMG_MAIN, $image_main);
	}

	public function getImageMain() : string
	{
		return $this->getImage(Category_ShopData::IMG_MAIN);
	}

	public function getImageMainUrl() : string
	{
		return $this->getImageUrl(Category_ShopData::IMG_MAIN);
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Category_ShopData::IMG_MAIN, $max_w, $max_h );
	}

	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->setImage( Category_ShopData::IMG_PICTOGRAM, $image_pictogram );
	}

	public function getImagePictogram() : string
	{
		return $this->getImage(Category_ShopData::IMG_PICTOGRAM);
	}

	public function getImagePictogramUrl() : string
	{
		return $this->getImageUrl(Category_ShopData::IMG_PICTOGRAM);
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl(Category_ShopData::IMG_PICTOGRAM, $max_w, $max_h);
	}

	public function getImageDeleteForm( string $image_class ) : Form|null
	{
		$property_name = 'image_'.$image_class;
		$img = $this->{$property_name};

		$category = Category::get($this->category_id);
		if(
			!$img &&
			(
				$category->getType()==Category::CATEGORY_TYPE_LINK ||
				$category->getType()==Category::CATEGORY_TYPE_VIRTUAL
			)
		) {
			return null;
		}

		if(!isset($this->image_delete_forms[$image_class])) {

			$form = new Form('image_delete_'.$this->getImageEntity().'_'.$image_class.'_'.$this->getShopKey(), []);

			$this->image_delete_forms[$image_class] = $form;
		}

		return $this->image_delete_forms[$image_class];

	}

	public function getProductIds() : array
	{
		if(!$this->product_ids) {
			return [];
		}

		return explode(',', $this->product_ids);
	}

	public function getProductIdsRaw() : string
	{
		return $this->product_ids;
	}

	public function setProductIds( array $product_ids, $save=false ) : void
	{
		$this->product_ids = implode(',', $product_ids);
		$this->visible_products_count = count($product_ids);

		if($save) {
			$this->updateData([
				'product_ids' => $this->product_ids,
				'visible_products_count' => $this->visible_products_count
			],[
				'category_id' => $this->category_id,
				'AND',
				$this->getShop()->getWhere()
			]);
		}
	}

	public function getPath() : array
	{
		if(!$this->path) {
			return [];
		}

		return explode(',', $this->path );
	}

	/**
	 *
	 * @return Category[]|DataModel_Fetch_Instances
	 */
	public function getChildren() : DataModel_Fetch_Instances|array
	{
		if($this->_children!==null) {
			return $this->_children;
		}

		$this->_children = [];

		if($this->children) {
			$ch_ids = explode(',', $this->children);
			$this->_children = Category::fetchInstances(['id'=>$ch_ids]);
		}

		return $this->_children;
	}

}