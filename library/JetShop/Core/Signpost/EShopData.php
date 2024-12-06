<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\JetShopEntity_Definition;
use JetApplication\Entity_WithEShopData_EShopData;
use JetApplication\Signpost;
use JetApplication\Signpost_Category;

#[DataModel_Definition(
	name: 'signposts_eshop_data',
	database_table_name: 'signposts_eshop_data',
	parent_model_class: Signpost::class
)]
abstract class Core_Signpost_EShopData extends Entity_WithEShopData_EShopData {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
	)]
	#[JetShopEntity_Definition(
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
	#[JetShopEntity_Definition(
		is_description: true
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'URL parameter:',
	)]
	#[JetShopEntity_Definition(
		is_description: true
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

	public function getURL() : string
	{
		return $this->getEshop()->getURL( [$this->URL_path_part] );
	}
	

	public function getURLPathPart(): string
	{
		return $this->URL_path_part;
	}
	
	public function setURLPathPart( string $URL_path_part ): void
	{
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
	
	public function afterAdd(): void
	{
		$this->generateURLPathPart();
	}
	
	
	public function generateURLPathPart() : void
	{
		if(!$this->entity_id) {
			return;
		}
		
		$this->URL_path_part = $this->_generateURLPathPart( $this->getName(), 't' );
		
		$where = $this->getEshop()->getWhere();
		$where[] = 'AND';
		$where['entity_id'] = $this->entity_id;
		
		
		static::updateData(
			['URL_path_part'=>$this->URL_path_part],
			$where
		);
		
	}
	
	
}