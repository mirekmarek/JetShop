<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;

use Jet\Form_Field_Color;
use Jet\Form_Field_File;
use Jet\Form_Field_File_UploadedFile;
use Jet\Form_Field_FileImage;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_Marketing_Banners;
use JetApplication\EShopEntity_Marketing;
use JetApplication\EShopEntity_Definition;
use JetApplication\Marketing_Banner;
use JetApplication\Marketing_BannerGroup;
use JetApplication\EShops;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'banners',
	database_table_name: 'banners',
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_Marketing_Banners::class
)]
abstract class Core_Marketing_Banner extends EShopEntity_Marketing implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Group:',
		select_options_creator: [
			Marketing_BannerGroup::class,
			'getScope'
		]
	)]
	protected int $group_id = 0;
	
	protected ?Marketing_BannerGroup $group = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $position = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Text:'
	)]
	protected string $text = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_COLOR,
		label: 'Text color:',
		error_messages: [
			Form_Field_Color::ERROR_CODE_INVALID_FORMAT => 'Invalid value'
		]
	)]
	protected string $text_color = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'URL:'
	)]
	protected string $URL = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $image_main = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
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
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'SEO: No follow'
	)]
	protected bool $nofollow = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Open in new window'
	)]
	protected bool $open_in_new_window = false;
	
	
	
	public function getGroupId(): int
	{
		return $this->group_id;
	}
	
	public function setGroupId( int $group_id ): void
	{
		$this->group_id = $group_id;
	}
	
	public function getGroup(): Marketing_BannerGroup
	{
		if( !$this->group ) {
			$this->group = Marketing_BannerGroup::load( $this->group_id );
		}
		
		return $this->group;
	}
	
	public function getPosition(): int
	{
		return $this->position;
	}
	
	public function setPosition( int $position ): void
	{
		$this->position = $position;
	}
	
	
	public function getText(): string
	{
		return $this->text;
	}
	
	public function setText( string $text ): void
	{
		$this->text = $text;
	}
	
	public function getTextColor(): string
	{
		return $this->text_color;
	}
	
	public function setTextColor( string $text_color ): void
	{
		$this->text_color = $text_color;
	}
	
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
		$dir = SysConf_Path::getImages() . 'banners/' . $this->id . '/';
		if( !IO_Dir::exists( $dir ) ) {
			IO_Dir::create( $dir );
		}
		return $dir;
	}
	
	protected function getURI(): string
	{
		return SysConf_URI::getImages() . 'banners/' . $this->id . '/';
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
	
	public function isNofollow(): bool
	{
		return $this->nofollow;
	}
	
	public function setNofollow( bool $nofollow ): void
	{
		$this->nofollow = $nofollow;
	}
	
	public function isOpenInNewWindow(): bool
	{
		return $this->open_in_new_window;
	}
	
	public function setOpenInNewWindow( bool $open_in_new_window ): void
	{
		$this->open_in_new_window = $open_in_new_window;
	}
	
	
	/**
	 * @param EShop $eshop
	 * @param Marketing_BannerGroup $group
	 * @return static[]
	 */
	public static function getByGroup( EShop $eshop, Marketing_BannerGroup $group ): array
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['group_id'] = $group->getId();
		
		return static::fetch( ['banners' => $where], order_by: 'position', item_key_generator: function( Marketing_Banner $banner ) {
			return $banner->getId();
		} );
	}
	
	/**
	 * @param Marketing_BannerGroup $group
	 * @param EShop|null $eshop
	 *
	 * @return static[]
	 */
	public static function getActiveByGroup( Marketing_BannerGroup $group, ?EShop $eshop=null ): array
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['group_id'] = $group->getId();
		$where[] = 'AND';
		$where['is_active'] = true;
		
		$banners = static::fetch(
			['banners' => $where],
			order_by: 'position',
			item_key_generator: function( Marketing_Banner $banner ) {
				return $banner->getId();
			} );
		
		$result = [];
		foreach($banners as $banner) {
			if($banner->isActive()) {
				$result[] = $banner;
			}
		}
		
		return $result;
	}
	
	public static function getActive( string $internal_code, ?EShop $eshop=null ): ?static
	{
		$eshop = $eshop?:EShops::getCurrent();
		
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['internal_code'] = $internal_code;
		$where[] = 'AND';
		$where['is_active'] = true;
		
		$banners = static::fetch(
			['banners' => $where],
			order_by: 'position',
			item_key_generator: function( Marketing_Banner $banner ) {
				return $banner->getId();
			} );
		
		$result = [];
		foreach($banners as $banner) {
			if($banner->isActive()) {
				return $banner;
			}
		}
		
		return null;
	}
	
	
	protected function setupAddForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	protected function setupForm( Form $form ) : void
	{
		$form->removeField('relevance_mode');
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
	
}