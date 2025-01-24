<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\Admin_Managers_MarketingBannerGroups;
use JetApplication\Entity_Common;
use JetApplication\JetShopEntity_Definition;


#[DataModel_Definition(
	name: 'banner_groups',
	database_table_name: 'banner_groups',
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_MarketingBannerGroups::class
)]
abstract class Core_Marketing_BannerGroup extends Entity_Common implements Admin_Entity_Common_Interface
{
	use Admin_Entity_Common_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has main image'
	)]
	protected bool $has_main_image = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Main image width:'
	)]
	protected int $main_image_w = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Main image height:'
	)]
	protected int $main_image_h = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has mobile image'
	)]
	protected bool $has_mobile_image = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Mobile image width:'
	)]
	protected int $mobile_image_w = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Mobile image height:'
	)]
	protected int $mobile_image_h = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has main video'
	)]
	protected bool $has_main_video = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Main video width:'
	)]
	protected int $main_video_w = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Main video height:'
	)]
	protected int $main_video_h = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has mobile video'
	)]
	protected bool $has_mobile_video = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Mobile video width:'
	)]
	protected int $mobile_video_w = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Mobile video height:'
	)]
	protected int $mobile_video_h = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has text'
	)]
	protected bool $has_text = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has text color'
	)]
	protected bool $has_color = true;
	
	public static function getByCode( string $code ) : ?static
	{
		return static::load([
			'internal_code' => $code,
			'AND',
			'is_active' => true
		]);
	}
	
	public function getHasMainImage(): bool
	{
		return $this->has_main_image;
	}
	
	public function setHasMainImage( bool $has_main_image ): void
	{
		$this->has_main_image = $has_main_image;
	}
	
	public function getMainImageW(): int
	{
		return $this->main_image_w;
	}
	
	public function setMainImageW( int $main_image_w ): void
	{
		$this->main_image_w = $main_image_w;
	}
	
	public function getMainImageH(): int
	{
		return $this->main_image_h;
	}
	
	public function setMainImageH( int $main_image_h ): void
	{
		$this->main_image_h = $main_image_h;
	}
	
	public function getHasMobileImage(): bool
	{
		return $this->has_mobile_image;
	}
	
	public function setHasMobileImage( bool $has_mobile_image ): void
	{
		$this->has_mobile_image = $has_mobile_image;
	}
	
	public function getMobileImageW(): int
	{
		return $this->mobile_image_w;
	}
	
	public function setMobileImageW( int $mobile_image_w ): void
	{
		$this->mobile_image_w = $mobile_image_w;
	}
	
	public function getMobileImageH(): int
	{
		return $this->mobile_image_h;
	}
	
	public function setMobileImageH( int $mobile_image_h ): void
	{
		$this->mobile_image_h = $mobile_image_h;
	}
	
	public function getHasMainVideo(): bool
	{
		return $this->has_main_video;
	}
	
	public function setHasMainVideo( bool $has_main_video ): void
	{
		$this->has_main_video = $has_main_video;
	}
	
	public function getMainVideoW(): int
	{
		return $this->main_video_w;
	}
	
	public function setMainVideoW( int $main_video_w ): void
	{
		$this->main_video_w = $main_video_w;
	}
	
	public function getMainVideoH(): int
	{
		return $this->main_video_h;
	}
	
	public function setMainVideoH( int $main_video_h ): void
	{
		$this->main_video_h = $main_video_h;
	}
	
	public function getHasMobileVideo(): bool
	{
		return $this->has_mobile_video;
	}
	
	public function setHasMobileVideo( bool $has_mobile_video ): void
	{
		$this->has_mobile_video = $has_mobile_video;
	}
	
	public function getMobileVideoW(): int
	{
		return $this->mobile_video_w;
	}
	
	public function setMobileVideoW( int $mobile_video_w ): void
	{
		$this->mobile_video_w = $mobile_video_w;
	}
	
	public function getMobileVideoH(): int
	{
		return $this->mobile_video_h;
	}

	public function setMobileVideoH( int $mobile_video_h ): void
	{
		$this->mobile_video_h = $mobile_video_h;
	}
	
	public function getHasText(): bool
	{
		return $this->has_text;
	}
	
	public function setHasText( bool $has_text ): void
	{
		$this->has_text = $has_text;
	}
	
	public function getHasColor(): bool
	{
		return $this->has_color;
	}
	
	public function setHasColor( bool $has_color ): void
	{
		$this->has_color = $has_color;
	}
}