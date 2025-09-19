<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Application_Service_Admin;
use JetApplication\ImageGallery;
use JetApplication\ImageGallery_Image;

abstract class Core_ImageGallery {
	
	protected string $entity_type;
	protected int $entity_id;
	
	/**
	 * @var ImageGallery_Image[]
	 */
	protected ?array $images = null;
	
	
	public function __construct( string $entity_type, int $entity_id )
	{

		$this->entity_type = $entity_type;
		$this->entity_id = $entity_id;
	}
	
	public function getEntityType(): string
	{
		return $this->entity_type;
	}
	
	public function getEntityId(): int
	{
		return $this->entity_id;
	}
	
	
	
	/**
	 * @return ImageGallery_Image[]
	 */
	public function getImages() : array
	{
		if( $this->images===null ) {
			$this->images = ImageGallery_Image::getImages( $this->getEntityType(), $this->getEntityId() );
		}
		
		return $this->images;
	}
	
	public function getImage( int $index ) : ?ImageGallery_Image
	{
		$this->getImages();
		
		return $this->images[$index]??null;
	}
	
	public function getImageById( int $id ) : ?ImageGallery_Image
	{
		$this->getImages();
		
		foreach($this->images as $image) {
			if($image->getId()==$id) {
				return $image;
			}
		}
		
		return null;
	}
	
	public function deleteImage( int $id ) : ?ImageGallery_Image
	{
		$this->getImages();
		
		$deleted_image = $this->getImageById( $id );
		if(!$deleted_image) {
			return null;
		}
		
		$deleted_image->delete();
		
		$this->images = null;
		$this->getImages();
		
		$index = 0;
		foreach($this->images as $img) {
			$img->setImageIndex( $index );
			$img->save();
			$index++;
		}
		
		return $deleted_image;
	}
	
	public function addImage( string $file ) : void
	{
		$this->getImages();
		
		$new_image = new ImageGallery_Image();
		$new_image->setImageGalleryEntityType( $this->getEntityType() );
		$new_image->setImageGalleryEntityId( $this->getEntityId() );
		$new_image->setImageFile( $file );
		$new_image->setImageIndex( count($this->images) );
		$new_image->save();
		
		$this->images[] = $new_image;
	}
	
	public function sortImages( array $image_ids ) : void
	{
		$image_ids = array_unique($image_ids);
		$this->getImages();
		
		$i = 0;
		foreach($image_ids as $id) {
			$image = $this->getImageById( $id );
			if(!$image) {
				continue;
			}
			
			$image->setImageIndex( $i );
			$image->save();
			
			$i++;
		}
		
		$this->images = null;
	}
	
	public function uploadImages( array $images ) : void
	{
		Application_Service_Admin::Image()->uploadImageGallery( $this, $images );
	}
	
	
	public function cloneImages( ImageGallery $source_gallery ) : void
	{
		$images = [];
		foreach( $source_gallery->getImages() as $source_image) {
			
			$image_path = $source_image->getPath();
			
			$path_i = pathinfo( $source_image->getImageFile() );
			$file_name = $path_i['filename'].'_c.'.$path_i['extension'];
			
			$images[$image_path] = $file_name;
		}
		
		$this->uploadImages( $images );
	}
	
}