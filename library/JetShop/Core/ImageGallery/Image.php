<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Application_Service_EShop;
use JetApplication\EShopEntity_Basic;

#[DataModel_Definition(
	name: 'image_gallery',
	database_table_name: 'image_gallery',
)]
abstract class Core_ImageGallery_Image extends EShopEntity_Basic
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $entity_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected string $entity_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $image_index = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $image_file = '';
	
	protected string $key = '';
	
	
	/**
	 * @param string $entity_type
	 * @param int $entity_id
	 * @return static[]
	 */
	public static function getImages( string $entity_type, int $entity_id ): array
	{
		$_images = static::fetch(
			where_per_model: [''=>[
				'entity_type' => $entity_type,
				'AND',
				'entity_id' => $entity_id
			]],
			order_by: ['image_index']
		);
		
		$images = [];
		
		foreach($_images as $img) {
			$images[] = $img;
		}
		
		return $images;
	}
	
	public function getImageGalleryEntityType(): string
	{
		return $this->entity_type;
	}
	
	public function setImageGalleryEntityType( string $entity_type ): void
	{
		$this->entity_type = $entity_type;
	}
	
	
	public function setImageGalleryEntityId( int $id ) : void
	{
		$this->entity_id = $id;
	}
	
	public function getImageGalleryEntityId() : string
	{
		return $this->entity_id;
	}
	
	public function setImageIndex( int $value ) : void
	{
		$this->image_index = $value;
	}
	
	public function getImageIndex() : int
	{
		return $this->image_index;
	}
	
	public function setImageFile( string $value ) : void
	{
		$this->image_file = $value;
	}
	
	public function getImageFile() : string
	{
		return $this->image_file;
	}
	
	public function getImageFileName() : string
	{
		return basename($this->image_file);
	}
	
	public function __toString() : string
	{
		return $this->image_file;
	}
	
	public function afterUpdate(): void
	{
	}
	
	public function afterDelete(): void
	{
	}
	
	public function afterAdd(): void
	{
	}
	
	public function getThumbnailUrl( int $max_w, int $max_h ): string
	{
		return Application_Service_EShop::Image()->getThumbnailUrl(
			$this->getImageFile(),
			$max_w,
			$max_h
		);
	}
	
	public function getURL(): string
	{
		return Application_Service_EShop::Image()->getUrl(
			$this->getImageFile()
		);
	}
	
	public function getPath(): string
	{
		return Application_Service_EShop::Image()->getPath(
			$this->getImageFile()
		);
		
	}
}
