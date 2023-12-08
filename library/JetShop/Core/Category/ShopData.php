<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Category;
use JetApplication\Entity_WithIDAndShopData_ShopData;
use JetApplication\Shops;

/**
 *
 */
#[DataModel_Definition(
	name: 'category_shop_data',
	database_table_name: 'categories_shop_data',
	parent_model_class: Category::class,
)]
abstract class Core_Category_ShopData extends Entity_WithIDAndShopData_ShopData {
	
	
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
	protected string $name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Alternative name:'
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
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'H1:'
	)]
	protected string $seo_h1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
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
	protected string $seo_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Keywords:'
	)]
	protected string $seo_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
	)]
	protected string $URL_path_part = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Keywords for internal fulltext:'
	)]
	protected string $internal_fulltext_keywords = '';

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
	
	
	
	public function activate(): void
	{
		parent::activate();
		Category::actualizeBranchProductAssoc( $this->root_id );
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		Category::actualizeBranchProductAssoc( $this->root_id );
	}
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority, bool $save=true ): void
	{
		$this->priority = $priority;
		if($save) {
			$where = Shops::get($this->shop_code)->getWhere();
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
			$where = Shops::get($this->shop_code)->getWhere();
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

	public function getSeoH1() : string
	{
		return $this->seo_h1;
	}

	public function setSeoH1( string $seo_h1 ) : void
	{
		$this->seo_h1 = $seo_h1;
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
					'entity_id' => $this->entity_id,
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
		if(!$this->entity_id) {
			return;
		}

		$this->URL_path_part = $this->_generateURLPathPart( $this->name, 'c' );
	}

	

	
	
	
	public function setImageMain( string $image_main ) : void
	{
		$this->image_main = $image_main;
	}

	public function getImageMain() : string
	{
		return $this->image_main;
	}


	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->image_pictogram = $image_pictogram;
	}

	public function getImagePictogram() : string
	{
		return $this->image_pictogram;
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
				$this->getShop()->getWhere()
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
	
	public function getChildren() : array
	{
		return explode(',', $this->children);
	}
	
	public function getBranchChildren(): array
	{
		return explode(',', $this->branch_children);
	}
	
	public function getActiveBranchChildren(): array
	{
		return explode(',', $this->active_branch_children);
	}

}