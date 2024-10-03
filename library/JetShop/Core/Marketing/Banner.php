<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel;
use Jet\Form_Definition;
use Jet\Form_Field;

use Jet\Form_Field_Color;
use Jet\Form_Field_File_UploadedFile;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetApplication\Entity_Marketing;
use JetApplication\Marketing_Banner;
use JetApplication\Marketing_BannerGroup;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplicationModule\Admin\Marketing\BannerGroups\BannerGroup;


#[DataModel_Definition(
	name: 'banners',
	database_table_name: 'banners',
)]
abstract class Core_Marketing_Banner extends Entity_Marketing
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
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
	 * @param Shops_Shop $shop
	 * @param Marketing_BannerGroup $group
	 * @return static[]
	 */
	public static function getByGroup( Shops_Shop $shop, Marketing_BannerGroup $group ): array
	{
		$where = $shop->getWhere();
		$where[] = 'AND';
		$where['group_id'] = $group->getId();
		
		return static::fetch( ['banners' => $where], order_by: 'position', item_key_generator: function( Marketing_Banner $banner ) {
			return $banner->getId();
		} );
	}
	
	/**
	 * @param BannerGroup $group
	 * @param Shops_Shop|null $shop
	 *
	 * @return static[]
	 */
	public static function getActiveByGroup( BannerGroup $group, ?Shops_Shop $shop=null ): array
	{
		$shop = $shop?:Shops::getCurrent();
		
		$where = $shop->getWhere();
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
	
	public static function getActive( string $internal_code, ?Shops_Shop $shop=null ): ?static
	{
		$shop = $shop?:Shops::getCurrent();
		
		$where = $shop->getWhere();
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
	
}