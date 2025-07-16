<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_File;
use Jet\Form_Field_File_UploadedFile;
use Jet\Form_Field_FileImage;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetApplication\Category;
use JetApplication\EShop;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Marketing;
use Jet\DataModel;
use JetApplication\Admin_Managers_Marketing_CategoryBanners;
use JetApplication\Marketing_CategoryBanner_CategoryAssoc;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'category_banner',
	database_table_name: 'category_banners',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Category Banner',
	admin_manager_interface: Admin_Managers_Marketing_CategoryBanners::class
)]
abstract class Core_Marketing_CategoryBanner extends EShopEntity_Marketing implements
	EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;

	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len : 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'URL:'
	)]
	protected string $URL = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len : 255
	)]
	protected string $image_main = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len : 255
	)]
	protected string $image_mobile = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $video_main = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $video_mobile = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN
	)]
	protected string $_category_ids = '';
	
	
	
	public function getURL(): string
	{
		return $this->URL;
	}
	
	public function setURL( string $URL ): void
	{
		$this->URL = $URL;
	}
	
	protected function getDir(): string
	{
		$dir = SysConf_Path::getImages() . 'category_banners/' . $this->id . '/';
		if( !IO_Dir::exists( $dir ) ) {
			IO_Dir::create( $dir );
		}
		return $dir;
	}
	
	protected function getURI(): string
	{
		return SysConf_URI::getImages() . 'category_banners/' . $this->id . '/';
	}
	
	protected function _setFile(string $src_file_path, string $file_name, &$property) : void
	{
		$dir = $this->getDir();
		if( $property ) {
			$old_path = $dir . $property;
			if( IO_File::exists( $old_path ) ) {
				IO_File::delete( $old_path );
			}
		}
		
		IO_File::copy( $src_file_path, $dir . $file_name );
		
		$property = $file_name;
		$this->save();
	}
	
	public function setMainImage( string $src_file_path, string $file_name ) : void
	{
		$this->_setFile($src_file_path, $file_name, $this->image_main );
	}
	
	public function setMobileImage( string $src_file_path, string $file_name ) : void
	{
		$this->_setFile($src_file_path, $file_name, $this->image_mobile );
	}
	
	protected function setFile( Form_Field_File_UploadedFile $file, &$property ): void
	{
		$dir = $this->getDir();
		if( $property ) {
			$old_path = $dir . $property;
			if( IO_File::exists( $old_path ) ) {
				IO_File::delete( $old_path );
			}
		}
		
		IO_File::moveUploadedFile( $file->getTmpFilePath(), $dir . $file->getFileName() );
		
		$property = $file->getFileName();
		$this->save();
	}
	
	protected function deleteFile( &$property ): void
	{
		$dir = $this->getDir();
		if( $property ) {
			$old_path = $dir . $property;
			if( IO_File::exists( $old_path ) ) {
				IO_File::delete( $old_path );
			}
			$property = '';
			$this->save();
		}
		
	}
	
	public function getImageMain(): string
	{
		return $this->image_main;
	}
	
	public function getImageMainURI(): string
	{
		if( !$this->image_main ) {
			return '';
		}
		
		return $this->getURI() . rawurlencode( $this->image_main );
	}
	
	public function setImageMain( Form_Field_File_UploadedFile $file ): void
	{
		$this->setFile( $file, $this->image_main );
	}
	
	public function deleteImageMain(): void
	{
		$this->deleteFile( $this->image_main );
	}
	
	public function getImageMobile(): string
	{
		return $this->image_mobile;
	}
	
	public function getImageMobileURI(): string
	{
		if( !$this->image_mobile ) {
			return '';
		}
		
		return $this->getURI() . rawurlencode( $this->image_mobile );
	}
	
	public function setImageMobile( Form_Field_File_UploadedFile $file ): void
	{
		$this->setFile( $file, $this->image_mobile );
	}
	
	public function deleteImageMobile(): void
	{
		$this->deleteFile( $this->image_mobile );
	}
	
	public function getVideoMain(): string
	{
		return $this->video_main;
	}
	
	public function setVideoMain( Form_Field_File_UploadedFile $file ): void
	{
		$this->setFile( $file, $this->video_main );
	}
	
	public function deleteVideoMain(): void
	{
		$this->deleteFile( $this->video_main );
	}
	
	public function getVideoMainURI(): string
	{
		if( !$this->video_main ) {
			return '';
		}
		
		return $this->getURI() . rawurlencode( $this->video_main );
	}
	
	
	public function getVideoMobile(): string
	{
		return $this->video_mobile;
	}
	
	public function setVideoMobile( Form_Field_File_UploadedFile $file ): void
	{
		$this->setFile( $file, $this->video_mobile );
	}
	
	public function deleteVideoMobile(): void
	{
		$this->deleteFile( $this->video_mobile );
	}
	
	public function getVideoMobileURI(): string
	{
		if( !$this->video_mobile ) {
			return '';
		}
		
		return $this->getURI() . rawurlencode( $this->video_mobile );
	}
	
	
	protected array $upload_forms = [];
	
	protected function catchUploadForm( Form $form ) : bool
	{
		if($form->catchInput()) {
			$form->catch();
			
			return true;
		}
		return false;
	}
	
	protected function getUploadImageForm($form_name, callable $setter ) : Form
	{
		if(!isset($this->upload_forms[$form_name])) {
			$image = new Form_Field_FileImage('image');
			
			$image->setFieldValueCatcher( function() use ($image, $setter) {
				foreach($image->getValidFiles() as $file) {
					$setter( $file );
				}
			} );
			$this->upload_forms[$form_name] = new Form($form_name, [$image]);
		}
		
		return $this->upload_forms[$form_name];
	}
	
	protected function getUploadVideoForm($form_name, callable $setter ) : Form
	{
		if(!isset($this->upload_forms[$form_name])) {
			$video = new Form_Field_File('video');
			$video->setAllowedMimeTypes([
				'video/mp4'
			]);
			$video->setErrorMessages([
				Form_Field_File::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Please upload video'
			]);
			
			$video->setFieldValueCatcher( function() use ($video, $setter) {
				foreach($video->getValidFiles() as $file) {
					$setter( $file );
				}
			} );
			$this->upload_forms[$form_name] = new Form($form_name, [$video]);
		}
		
		return $this->upload_forms[$form_name];
	}
	
	public function getUploadForm_MainImage() : Form
	{
		return $this->getUploadImageForm(
			'upload_form_main_image',
			function(Form_Field_File_UploadedFile $file) {
				$this->setImageMain($file);
				$this->save();
			}
		);
	}
	
	public function catchUploadForm_MainImage() : bool
	{
		return $this->catchUploadForm( $this->getUploadForm_MainImage() );
	}
	
	public function getUploadForm_MobileImage() : Form
	{
		return $this->getUploadImageForm(
			'upload_form_mobile_image',
			function(Form_Field_File_UploadedFile $file) {
				$this->setImageMobile($file);
				$this->save();
			}
		);
	}
	
	public function catchUploadForm_MobileImage() : bool
	{
		return $this->catchUploadForm( $this->getUploadForm_MobileImage() );
	}
	
	
	
	
	public function getUploadForm_MainVideo() : Form
	{
		return $this->getUploadVideoForm(
			'upload_form_main_video',
			function(Form_Field_File_UploadedFile $file) {
				$this->setVideoMain($file);
				$this->save();
			}
		);
	}
	
	public function catchUploadForm_MainVideo() : bool
	{
		return $this->catchUploadForm( $this->getUploadForm_MainVideo() );
	}
	
	public function getUploadForm_MobileVideo() : Form
	{
		return $this->getUploadVideoForm(
			'upload_form_mobile_video',
			function(Form_Field_File_UploadedFile $file) {
				$this->setVideoMobile($file);
				$this->save();
			}
		);
	}
	
	public function catchUploadForm_MobileVideo() : bool
	{
		return $this->catchUploadForm( $this->getUploadForm_MobileVideo() );
	}
	
	
	public function getCategoryIds() : array
	{
		return Marketing_CategoryBanner_CategoryAssoc::getCategoryIds( $this->getId() );
	}
	
	public function setCategoryIds( array $category_ids ): void
	{
		Marketing_CategoryBanner_CategoryAssoc::setAssoc( $this->getId(), $category_ids );
	}
	
	/**
	 * @return Category[]
	 */
	public function getCategories() : array
	{
		$ids = $this->getCategoryIds();
		if(!$ids) {
			return [];
		}
		
		
		return Category::fetch( [''=>[
			'id' => $ids
		]] );
	}
	
	public static function getIdListByCategory( int|array $category_id ) : array
	{
		return Marketing_CategoryBanner_CategoryAssoc::getBannerIds( $category_id );
	}
	
	
	/**
	 * @param int|array $category_id
	 * @param EShop|null $eshop
	 * @return static[]
	 */
	public static function getListByCategory( int|array $category_id, ?EShop $eshop=null ) : array
	{
		$banner_ids = static::getIdListByCategory( $category_id );
		if(!$banner_ids) {
			return [];
		}
		
		return static::getActiveList( $banner_ids, $eshop );
	}
	
	public function setupEditForm( Form $form ): void
	{
		$field = $form->getField('_category_ids');
		$field->setDefaultValue( implode(',', $this->getCategoryIds() ) );
		$field->setFieldValueCatcher( function( $value ) {
			$this->setCategoryIds( $value?explode(',', $value):[] );
		});
		
	}
	
}