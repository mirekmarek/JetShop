<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

trait Core_Product_Trait_Images
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_0 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_2 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_3 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_4 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_5 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_6 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_7 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_8 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_9 = '';

	
	public function getImageEntity() : string
	{
		return 'product';
	}
	
	public function getImageObjectId() : int|string
	{
		return $this->id;
	}
	
	public function getImage( int $i = 0 ) : string
	{
		if(
			!$this->{"image_{$i}"} &&
			$this->type==Product::PRODUCT_TYPE_VARIANT
		) {
			$master = $this->getVariantMasterProduct();
			if($master) {
				return $master->getImage($i);
			}
		}
		
		return $this->{"image_{$i}"};
	}
	
	public function getImageRaw( int $i = 0 ) : string
	{
		return $this->{"image_{$i}"};
	}
	
	public function getImageUrl( int $i = 0 ) : string
	{
		return ImagesShared::getUrl( $this->getImage( $i ) );
	}
	
	public function getImageUrlRaw( int $i = 0 ) : string
	{
		return ImagesShared::getUrl( $this->getImageRaw( $i ) );
	}
	
	public function getImageThumbnailUrl( int $max_w, int $max_h, int $i=0 ) : string
	{
		return ImagesShared::getThumbnailUrl( $this->getImage( $i ), $max_w, $max_h );
	}
	
	public function getImageThumbnailUrlRaw( int $max_w, int $max_h, int $i=0 ) : string
	{
		return ImagesShared::getThumbnailUrl( $this->getImageRaw( $i ), $max_w, $max_h );
	}
	
	
	public function setImage( int $i, string $img ) : void
	{
		$this->{"image_{$i}"} = $img;
	}
	
	
	public function uploadImages() : void
	{
		$current_images = [];
		
		for( $i=0; $i<Product::$max_image_count; $i++ ) {
			if($this->getImageRaw( $i )) {
				$current_images[] = $this->getImageRaw( $i );
			}
		}
		
		
		$new_images = [];
		foreach( $_FILES['images']['tmp_name'] as $tmp_name ) {
			if(
				!$tmp_name ||
				!@getimagesize( $tmp_name )
			) {
				continue;
			}
			
			$new_images[] = $tmp_name;
			
			if( (count($current_images)+count($new_images))>=Product::$max_image_count ) {
				break;
			}
		}
		
		$i = 0;
		foreach( $current_images as $current_image ) {
			$this->{"image_{$i}"} = $current_image;
			$i++;
		}
		
		foreach( $new_images as $new_image ) {
			if($i>=Product::$max_image_count) {
				break;
			}
			
			ImagesShared::uploadImage(
				$new_image,
				'product',
				$this->id,
				'image',
				$this->{"image_{$i}"}
			);
			
			$i++;
		}
		
	}
	
	public function deleteImages( array  $indexes ) : void
	{
		
		foreach($indexes as $i) {
			$i = (int)$i;
			
			$property = 'image_'.$i;
			if(!property_exists($this, $property)) {
				break;
			}
			
			if(!$this->{$property} ) {
				continue;
			}
			
			ImagesShared::deleteImage( $this->{$property} );
			
			$this->{$property} = '';
		}
		
		$current_images = [];
		
		for( $i=0; $i<Product::$max_image_count; $i++ ) {
			if($this->getImageRaw( $i )) {
				$current_images[] = $this->getImageRaw( $i );
			}
			
			$this->{"image_{$i}"} = '';
		}
		
		foreach($current_images as $i=>$image) {
			$this->{"image_{$i}"} = $image;
		}
		
	}
	
	public function sortImages( array $indexes ) : void
	{
		
		$current_images = [];
		
		for( $i=0; $i<Product::$max_image_count; $i++ ) {
			if($this->getImageRaw( $i )) {
				$current_images[] = $this->getImageRaw( $i );
			}
		}
		
		if(count($indexes)!=count($current_images)) {
			return;
		}
		
		
		$images = [];
		
		foreach($indexes as $i) {
			if(!isset($current_images[$i])) {
				return;
			}
			
			$images[] = $current_images[$i];
		}
		
		foreach($images as $i=>$image) {
			$this->{"image_{$i}"} = $image;
		}
		
	}

}