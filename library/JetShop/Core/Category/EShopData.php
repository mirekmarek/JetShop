<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_HasURL_Interface;
use JetApplication\EShopEntity_HasURL_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\Category;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'category_eshop_data',
	database_table_name: 'categories_eshop_data',
	parent_model_class: Category::class,
)]
#[EShopEntity_Definition(
	URL_template: '%NAME%-c-%ID%'
)]
abstract class Core_Category_EShopData extends EShopEntity_WithEShopData_EShopData implements
	EShopEntity_HasImages_Interface,
	EShopEntity_HasURL_Interface
{
	use EShopEntity_HasImages_Trait;
	use EShopEntity_HasURL_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $root_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $parent_id = 0;
	
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
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Alternative name:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $second_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type:  Form_Field::TYPE_WYSIWYG,
		label:  'Description:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $seo_title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $seo_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Keywords:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $seo_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
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
		type: DataModel::TYPE_STRING,
		max_len: 999999999,
	)]
	protected string $product_ids = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999999,
	)]
	protected string $branch_product_ids = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $products_count = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $branch_products_count = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $path = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $children = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $branch_children = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $active_branch_children = '';
	
	
	
	public function _activate(): void
	{
		parent::_activate();
		Category::actualizeBranchProductAssoc( $this->root_id );
	}
	
	public function _deactivate(): void
	{
		parent::_deactivate();
		Category::actualizeBranchProductAssoc( $this->root_id );
	}
	
	public function getURLNameDataSource(): string
	{
		return $this->URL_path_part ? : $this->name;
	}
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority, bool $save=true ): void
	{
		$this->priority = $priority;
		if($save) {
			$where = $this->getEshop()->getWhere();
			$where[] = 'AND';
			$where['entity_id'] = $this->entity_id;
			
			static::updateData(data: ['priority'=>$this->priority], where: $where);
		}
	}
	
	public function getParentId(): int
	{
		return $this->parent_id;
	}
	
	public function setParentId( int $parent_id, bool $save=true  ): void
	{
		$this->parent_id = $parent_id;
		if($save) {
			$where = EShops::get($this->eshop_code)->getWhere();
			$where[] = 'AND';
			$where['entity_id'] = $this->entity_id;
			
			static::updateData(data: ['parent_id'=>$this->parent_id], where: $where);
		}
	}



	public function getProductsCount() : int
	{
		return $this->products_count;
	}

	public function getBranchProductsCount() : int
	{
		return $this->branch_products_count;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function setName( string $name ) : void
	{
		if($this->name==$name) {
			return;
		}
		$this->name = $name;
		$this->generateURLPathPart();
	}

	public function getSecondName() : string
	{
		return $this->second_name;
	}

	public function setSecondName( string $second_name ) : void
	{
		$this->second_name = $second_name;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getSeoTitle() : string
	{
		return $this->seo_title;
	}

	public function setSeoTitle( string $seo_title ) : void
	{
		$this->seo_title = $seo_title;
	}

	public function getSeoDescription() : string
	{
		return $this->seo_description;
	}

	public function setSeoDescription( string $seo_description ) : void
	{
		$this->seo_description = $seo_description;
	}

	public function getSeoKeywords() : string
	{
		return $this->seo_keywords;
	}

	public function setSeoKeywords( string $seo_keywords ) : void
	{
		$this->seo_keywords = $seo_keywords;
	}

	public function getURLPathPart() : string
	{
		return $this->URL_path_part;
	}

	public function setURLPathPart( string $URL_path_part, bool $save=true ) : void
	{
		$this->URL_path_part = $URL_path_part;
		if($save) {
			$this->updateData(
				[
					'URL_path_part'=>$URL_path_part
				],
				[
					'entity_id' => $this->entity_id,
					'AND',
					$this->getEshop()->getWhere()
				]
			);
		}
	}
	
	public function setImageMain( string $image_main ) : void
	{
		$this->image_main = $image_main;
	}

	public function getImageMain() : string
	{
		return $this->image_main;
	}
	
	public function getImageMainThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl('main', $max_w, $max_h);
	}
	
	public function getMainImageUrl(): string
	{
		return $this->getImageUrl('main');
	}
	

	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->image_pictogram = $image_pictogram;
	}

	public function getImagePictogram() : string
	{
		return $this->image_pictogram;
	}
	
	
	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl('pictogram', $max_w, $max_h);
	}
	
	public function getPictogramImageUrl(): string
	{
		return $this->getImageUrl('pictogram');
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
		$this->products_count = count($product_ids);

		if($save) {
			$this->updateData([
				'product_ids' => $this->product_ids,
				'visible_products_count' => $this->products_count
			],[
				'entity_id' => $this->entity_id,
				'AND',
				$this->getEshop()->getWhere()
			]);
		}
	}
	
	
	public function getBranchProductIds() : array
	{
		if(!$this->branch_product_ids) {
			return [];
		}
		
		return explode(',', $this->branch_product_ids);
	}
	
	public function getBranchProductIdsRaw() : string
	{
		return $this->branch_product_ids;
	}
	
	public function setBranchProductIds( array $product_ids, $save=false ) : void
	{
		$this->branch_product_ids = implode(',', $product_ids);
		$this->branch_products_count = count($product_ids);
		
		if($save) {
			$this->updateData([
				'branch_product_ids' => $this->product_ids,
				'branch_products_count' => $this->branch_products_count
			],[
				'entity_id' => $this->entity_id,
				'AND',
				$this->getEshop()->getWhere()
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
	
	public function getChildrenIds() : array
	{
		return explode(',', $this->children);
	}
	
	public function getBranchChildrenIds(): array
	{
		return explode(',', $this->branch_children);
	}
	
	public function getActiveBranchChildrenIds(): array
	{
		return explode(',', $this->active_branch_children);
	}
	
	protected ?array $_children = null;
	
	/**
	 * @return static[]
	 */
	public function getChildren() : array
	{
		if($this->_children===null) {
			$this->_children = [];
			

			if( ($ids = $this->getChildrenIds()) ) {
				$this->_children = static::getActiveList( $ids );
			}
		}
		
		return $this->_children;
		
	}

	public static function getActiveList( array $ids, ?EShop $eshop = null, array|string|null $order_by = null ): array
	{
		if(!$order_by) {
			$order_by = ['priority'];
		}
		
		$list = parent::getActiveList( $ids, $eshop, $order_by );
		foreach($list as $id=>$item) {
			if(!$item->getBranchProductsCount()) {
				unset($list[$id]);
			}
		}
		
		return $list;
	}
	
	protected static array $names = [];
	
	public static function getNames( EShop $eshop ) : array
	{
		$key = $eshop->getKey();
		
		if(!isset(static::$names[$key])) {
			
			$where = static::getActiveQueryWhere();
			
			$data = static::dataFetchAll(
				select:[
					'entity_id',
					'name',
				],
				where: $where,
				raw_mode: true
			);
			
			foreach( $data as $d ) {
				static::$names[$key][(int)$d['entity_id']] = $d['name'];
			}

		}
		
		
		return static::$names[$key];
	}
	
	public function getPathName( bool $as_array=false, string $path_str_glue=' / ' ) : array|string
	{
		$result = [];
		
		$names = static::getNames( $this->getEshop() );
		
		foreach( $this->getPath() as $id ) {
			$name = $names[$id]??'';
			
			if($name) {
				$result[$id] = $name;
			}
		}
		
		if($as_array) {
			return $result;
		} else {
			return implode($path_str_glue, $result);
		}
	}
	
}