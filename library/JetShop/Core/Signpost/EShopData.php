<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Category_EShopData;
use JetApplication\EShop;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_HasURL_Interface;
use JetApplication\EShopEntity_HasURL_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Product_EShopData;
use JetApplication\Signpost;
use JetApplication\Signpost_Category;

#[DataModel_Definition(
	name: 'signposts_eshop_data',
	database_table_name: 'signposts_eshop_data',
	parent_model_class: Signpost::class
)]
#[EShopEntity_Definition(
	URL_template: '%NAME%-t-%ID%'
)]
abstract class Core_Signpost_EShopData extends EShopEntity_WithEShopData_EShopData implements EShopEntity_HasURL_Interface, EShopEntity_HasImages_Interface
{
	use EShopEntity_HasImages_Trait;
	use EShopEntity_HasURL_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $name = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Description:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $URL_path_part = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_main = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_pictogram = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $priority = 0;
	
	protected ?array $category_ids = null;
	protected ?array $categories = null;
	protected ?array $all_product_ids = null;
	protected ?array $active_product_ids = null;
	
	
	public function getURLNameDataSource(): string
	{
		return $this->name;
	}
	
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		if($this->name==$name) {
			return;
		}
		
		$this->name = $name;
		$this->generateURLPathPart();
	}
	
	
	
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}
	
	
	
	public function getImageMain(): string
	{
		return $this->image_main;
	}
	
	
	public function setImageMain( string $image_main ): void
	{
		$this->image_main = $image_main;
	}
	
	public function getImageMainThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl('main', $max_w, $max_h);
	}
	
	
	public function getImagePictogram(): string
	{
		return $this->image_pictogram;
	}
	
	
	public function setImagePictogram( string $image_pictogram ): void
	{
		$this->image_pictogram = $image_pictogram;
	}
	
	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl('pictogram', $max_w, $max_h);
	}
	
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
	
	
	public function getCategoryIds() : array|bool
	{
		if($this->category_ids===null) {
			$this->category_ids = Signpost_Category::dataFetchCol(
				select:['category_id'],
				where: ['signpost_id'=>$this->entity_id],
				order_by: ['priority'],
				raw_mode: true
			);
		}
		
		return $this->category_ids;
	}
	
	
	/**
	 * @return Category_EShopData[]
	 */
	public function getCategories(): array
	{
		if($this->categories===null) {
			$this->categories = [];
			$ids = $this->getCategoryIds();
			if($ids) {
				$this->categories = Category_EShopData::getActiveList( $ids, $this->getEshop() );
			}
		}
		
		return $this->categories;
	}
	
	/**
	 * @param ?EShop $eshop
	 *
	 * @return static[]
	 */
	public static function prefetchAllActive( ?EShop $eshop=null ) : array
	{
		$_signposts = static::getAllActive(order_by: 'priority');
		$signpost_ids = [];
		$signposts = [];
		foreach ($_signposts as $signpost) {
			$signpost_ids[] = $signpost->getId();
			$signposts[$signpost->getId()] = $signpost;
		}
		
		$_category_map = Signpost_Category::dataFetchAll(
			select:['category_id', 'signpost_id'],
			where: ['signpost_id'=>$signpost_ids],
			order_by: ['priority'],
			raw_mode: true
		);
		
		
		$category_ids = [];
		$category_map = [];
		foreach($_category_map as $m) {
			$signpost_id = $m['signpost_id'];
			$category_id = $m['category_id'];
			
			$category_ids[$category_id] = $category_id;
			$category_map[$signpost_id][] = $category_id;
		}
		
		
		$_categories = Category_EShopData::getActiveList( $category_ids, $eshop );
		$categories = [];
		
		foreach($_categories as $category) {
			$categories[$category->getId()] = $category;
		}
		
		foreach($signposts as $s_id=>$signpost) {
			$signpost->categories = [];
			foreach($category_map[$s_id] as $category_id) {
				if(isset($categories[$category_id])) {
					$signpost->categories[$category_id] = $categories[$category_id];
				}
			}
		}
		
		return $signposts;
		
	}
	
	public function getAllProductIds() : array
	{
		if( $this->all_product_ids===null ) {
			$this->all_product_ids = [];
			
			foreach( $this->getCategories() as $sc ) {
				
				$ids = $sc->getBranchProductIds();

				if($ids) {
					$this->all_product_ids += $sc->getBranchProductIds();
				}
			}
			
			$this->all_product_ids = array_unique( $this->all_product_ids );
		}
		
		return $this->all_product_ids;
	}
	
	public function getActiveProductIds() : array
	{
		if($this->active_product_ids===null) {
			$this->active_product_ids = [];
			
			if($this->getAllProductIds()) {
				$this->active_product_ids = Product_EShopData::getActiveProductsIds(
					$this->getEshop(),
					$this->getAllProductIds()
				);
			}
		}
		
		return $this->active_product_ids;
	}
	
	public function getActiveProductsCount() : int
	{
		return count( $this->getActiveProductIds() );
		
	}
	
}