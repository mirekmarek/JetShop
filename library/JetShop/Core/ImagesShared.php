<?php
namespace JetShop;
use Jet\Data_Image;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\Form_Field_FileImage;
use Jet\Form;

use JetApplication\ImagesShared;

abstract class Core_ImagesShared
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

	public static function getThbRootDir( string $entity, string $image_class ) : string
	{
		return $entity.'/'.$image_class.'/'.ImagesShared::getThbDir().'/';

	}

	public static function getImageDirPath( string $entity, string $image_class, int|string $object_id ) : string
	{
		return $entity.'/'.$image_class.'/'.ImagesShared::calcNumericPath($object_id).'/';
	}

	public static function uploadImage( string $tmp_path, string $entity, int|string $object_id, string $image_class, string &$object_property ) : void
	{
		$image_info = getimagesize($tmp_path);
		if(!$image_info) {
			return;
		}

		$target_path = ImagesShared::getImageDirPath( $entity, $image_class, $object_id ).uniqid().uniqid().image_type_to_extension($image_info[2]);

		$target_full_path = ImagesShared::getRootPath().$target_path;
		$target_dir = dirname($target_full_path);
		if(!IO_Dir::exists($target_dir)) {
			IO_Dir::create($target_dir);
		}

		IO_File::moveUploadedFile( $tmp_path, $target_full_path );

		if($object_property) {
			ImagesShared::deleteImage( $object_property );
		}

		$object_property = $target_path;
	}

	public static function takeImage( string $src_path, string $entity, int $object_id, string $image_class,  string &$object_property ) : void
	{
		$image_info = getimagesize($src_path);
		if(!$image_info) {
			return;
		}

		$target_path = ImagesShared::getImageDirPath( $entity, $image_class, $object_id ).uniqid().uniqid().image_type_to_extension($image_info[2]);

		$target_full_path = ImagesShared::getRootPath().$target_path;
		$target_dir = dirname($target_full_path);
		if(!IO_Dir::exists($target_dir)) {
			IO_Dir::create($target_dir);
		}

		IO_File::copy( $src_path, $target_full_path );

		if($object_property) {
			ImagesShared::deleteImage( $object_property );
		}

		$object_property = $target_path;
	}

	public static function cloneImage( string $source_path, string $entity, int $object_id, string $image_class, string &$object_property ) : void
	{
		$image_info = getimagesize($source_path);
		if(!$image_info) {
			return;
		}

		$target_path = ImagesShared::getImageDirPath( $entity, $image_class, $object_id ).uniqid().uniqid().image_type_to_extension($image_info[2]);

		IO_File::copy( $source_path, ImagesShared::getRootPath().$target_path );

		$object_property = $target_path;
	}

	public static function deleteImage( string $path ) : void
	{
		if(!$path) {
			return;
		}

		$_path = $path;

		$path = explode('/', $path);
		
		$entity = array_shift( $path );
		$image_class = array_shift( $path );

		$path = implode('/', $path);

		$thb_dir = ImagesShared::getRootPath().ImagesShared::getThbRootDir( $entity, $image_class );

		$sizes_list = IO_Dir::getList( $thb_dir, '*', true, false );

		foreach( $sizes_list as $root_path=>$size ) {
			$__path = $root_path.$path;
			if(IO_File::exists($__path)) {
				IO_File::delete( $__path );
			}
		}

		$__path = ImagesShared::getRootPath().$_path;
		if(IO_File::exists($__path)) {
			IO_File::delete( $__path );
		}
	}

	public static function getUrl( string $path ) : string
	{
		if(!$path) {
			return '';
		}

		return ImagesShared::getRootUrl().$path;
	}

	public static function getThumbnailUrl( string $path, int $max_w, int $max_h ) : string
	{
		$_path = $path;

		if(!$path) {
			return '';
		}

		$path = explode('/', $path);


		$entity = array_shift( $path );
		$image_class = array_shift( $path );

		$path = implode('/', $path);

		$thb_path = ImagesShared::getThbRootDir( $entity, $image_class ).$max_w.'x'.$max_h.'/'.$path;

		$thb_source_path = ImagesShared::getRootPath().$_path;
		$thb_target_path = ImagesShared::getRootPath().$thb_path;

		$url = ImagesShared::getRootUrl().$thb_path;

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

	public static function generateUploadForm( string $entity, string $image_class ) : Form
	{
		$image_field = new Form_Field_FileImage('image');
		$image_field->setErrorMessages([
			Form_Field_FileImage::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Please upload image',
			Form_Field_FileImage::ERROR_CODE_FILE_IS_TOO_LARGE => 'File is too large'

		]);

		return new Form($entity.'_image_'.$image_class, [
			$image_field
		]);

	}

	public static function catchUploadForm( Form $form, string $entity, string $image_class, int|string $object_id, string &$object_property ) : bool
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
			ImagesShared::uploadImage(
				$image->getTmpFilePath(),
				$entity,
				$object_id,
				$image_class,
				$object_property
			);
		}

		return true;
	}


}