<?php
namespace JetShop;
use Jet\Data_Image;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\Form_Field_FileImage;
use Jet\Form;


abstract class Core_Images
{
	protected static string $root_path;

	protected static string $root_url;

	protected static string $thb_dir = '_thb';

	protected static string $wm_dir = '_wm';

	public static function getRootPath(): string
	{
		return self::$root_path;
	}

	public static function setRootPath( string $root_path ) : void
	{
		self::$root_path = $root_path;
	}

	public static function getRootUrl() : string
	{
		return self::$root_url;
	}

	public static function setRootUrl( string $root_url ) : void
	{
		self::$root_url = $root_url;
	}

	public static function getThbDir() : string
	{
		return self::$thb_dir;
	}

	public static function setThbDir( string $thb_dir ) : void
	{
		self::$thb_dir = $thb_dir;
	}

	public static function getWmDir() : string
	{
		return self::$wm_dir;
	}

	public static function setWmDir( string $wm_dir ) : void
	{
		self::$wm_dir = $wm_dir;
	}

	public static function calcNumericPath( int|string $object_id ) : string
	{
		if(is_string($object_id)) {
			return $object_id;
		}

		$map = [];

		$numerical_order = 100000;

		$number = $object_id;
		while( $numerical_order>=10 ) {
			$c = floor($number/$numerical_order);

			$map[$numerical_order] = $c;

			$number = $number-($c*$numerical_order);

			$numerical_order = $numerical_order/10;
		}

		return implode('/',$map);
	}

	public static function getThbRootDir( Shops_Shop $shop, string $entity, string $image_class ) : string
	{
		return $shop->getKey().'/'.$entity.'/'.$image_class.'/'.Images::getThbDir().'/';

	}

	public static function getImageDirPath( Shops_Shop $shop, string $entity, string $image_class, int|string $object_id ) : string
	{
		return $shop->getKey().'/'.$entity.'/'.$image_class.'/'.Images::calcNumericPath($object_id).'/';
	}

	public static function uploadImage( string $tmp_path, Shops_Shop $shop, string $entity, int|string $object_id, string $image_class, string &$object_property ) : void
	{
		$image_info = getimagesize($tmp_path);
		if(!$image_info) {
			return;
		}

		$target_path = Images::getImageDirPath( $shop, $entity, $image_class, $object_id ).uniqid().uniqid().image_type_to_extension($image_info[2]);

		$target_full_path = Images::getRootPath().$target_path;
		$target_dir = dirname($target_full_path);
		if(!IO_Dir::exists($target_dir)) {
			IO_Dir::create($target_dir);
		}

		IO_File::moveUploadedFile( $tmp_path, $target_full_path );

		if($object_property) {
			Images::deleteImage( $object_property );
		}

		$object_property = $target_path;
	}

	public static function takeImage( string $src_path, Shops_Shop $shop, string $entity, int $object_id, string $image_class,  string &$object_property ) : void
	{
		$image_info = getimagesize($src_path);
		if(!$image_info) {
			return;
		}

		$target_path = Images::getImageDirPath( $shop, $entity, $image_class, $object_id ).uniqid().uniqid().image_type_to_extension($image_info[2]);

		$target_full_path = Images::getRootPath().$target_path;
		$target_dir = dirname($target_full_path);
		if(!IO_Dir::exists($target_dir)) {
			IO_Dir::create($target_dir);
		}

		IO_File::copy( $src_path, $target_full_path );

		if($object_property) {
			Images::deleteImage( $object_property );
		}

		$object_property = $target_path;
	}

	public static function cloneImage( string $source_path, Shops_Shop $shop, string $entity, int $object_id, string $image_class, string &$object_property ) : void
	{
		$image_info = getimagesize($source_path);
		if(!$image_info) {
			return;
		}

		$target_path = Images::getImageDirPath( $shop, $entity, $image_class, $object_id ).uniqid().uniqid().image_type_to_extension($image_info[2]);

		IO_File::copy( $source_path, Images::getRootPath().$target_path );

		$object_property = $target_path;
	}

	public static function deleteImage( string $path ) : void
	{
		if(!$path) {
			return;
		}

		$_path = $path;

		$path = explode('/', $path);

		$shop = Shops::get(array_shift( $path ));
		if(!$shop) {
			return;
		}

		$entity = array_shift( $path );
		$image_class = array_shift( $path );

		$path = implode('/', $path);

		$thb_dir = Images::getRootPath().Images::getThbRootDir( $shop, $entity, $image_class );

		$sizes_list = IO_Dir::getList( $thb_dir, '*', true, false );

		foreach( $sizes_list as $root_path=>$size ) {
			$__path = $root_path.$path;
			if(IO_File::exists($__path)) {
				IO_File::delete( $__path );
			}
		}

		$__path = Images::getRootPath().$_path;
		if(IO_File::exists($__path)) {
			IO_File::delete( $__path );
		}
	}

	public static function getUrl( string $path ) : string
	{
		if(!$path) {
			return '';
		}

		return Images::getRootUrl().$path;
	}

	public static function getThumbnailUrl( string $path, int $max_w, int $max_h ) : string
	{
		$_path = $path;

		if(!$path) {
			return '';
		}

		$path = explode('/', $path);

		$shop = Shops::get(array_shift( $path ));
		if(!$shop) {
			return '';
		}

		$entity = array_shift( $path );
		$image_class = array_shift( $path );

		$path = implode('/', $path);

		$thb_path = Images::getThbRootDir( $shop, $entity, $image_class ).$max_w.'x'.$max_h.'/'.$path;

		$thb_source_path = Images::getRootPath().$_path;
		$thb_target_path = Images::getRootPath().$thb_path;

		$url = Images::getRootUrl().$thb_path;

		if(!IO_File::exists($thb_source_path)) {
			return '';
		}

		if(!IO_File::exists($thb_target_path)) {
			$target_dir = dirname($thb_target_path);
			if(!IO_Dir::exists($target_dir)) {
				IO_Dir::create( $target_dir );
			}

			$image = new Data_Image( $thb_source_path );
			$image->createThumbnail( $thb_target_path, $max_w, $max_h );
		}



		return $url;
	}

	public static function generateUploadForm( string $entity, string $image_class, Shops_Shop $shop ) : Form
	{
		$image_field = new Form_Field_FileImage('image');
		$image_field->setErrorMessages([
			Form_Field_FileImage::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Please upload image',
			Form_Field_FileImage::ERROR_CODE_FILE_IS_TOO_LARGE => 'File is too large'

		]);

		return new Form($entity.'_image_'.$image_class.'_'.$shop->getKey(), [
			$image_field
		]);

	}

	public static function catchUploadForm( Form $form, string $entity, string $image_class, Shops_Shop $shop, int|string $object_id, string &$object_property ) : bool
	{
		if(
			!$form->catchInput() ||
			!$form->validate()
		) {
			return false;
		}
		/**
		 * @var Form_Field_FileImage $image_field
		 */
		$image_field = $form->getField('image');
		
		$images = $image_field->getValidFiles();

		foreach($images as $image) {
			Images::uploadImage(
				$image->getTmpFilePath(),
				$shop,
				$entity,
				$object_id,
				$image_class,
				$object_property
			);
		}

		return true;
	}


}