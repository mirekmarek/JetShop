<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;

use Jet\Form_Field;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_Marketing_ProductStickers;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_Marketing;
use JetApplication\EShops;
use JetApplication\EShopEntity_Definition;

#[DataModel_Definition(
	name: 'product_stickers',
	database_table_name: 'product_stickers',
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_Marketing_ProductStickers::class,
	images: [
		'pictogram_product_detail' => 'Pictogram - Product detail',
		'pictogram_product_listing' => 'Pictogram - Product listing',
	]
)]
abstract class Core_Marketing_ProductSticker extends EShopEntity_Marketing implements
	EShopEntity_HasImages_Interface,
	EShopEntity_Admin_Interface
{
	use EShopEntity_HasImages_Trait;
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 30,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_COLOR,
		label: 'Text color:'
	)]
	protected string $color_text = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 30,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_COLOR,
		label: 'Background color:'
	)]
	protected string $color_background = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:'
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Text:'
	)]
	protected string $text = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_pictogram_product_listing = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_pictogram_product_detail = '';
	
	
	public function getColorText(): string
	{
		return $this->color_text;
	}
	
	public function setColorText( string $color_text ): void
	{
		$this->color_text = $color_text;
	}
	
	public function getColorBackground(): string
	{
		return $this->color_background;
	}
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
	
	
	public function setColorBackground( string $color_background ): void
	{
		$this->color_background = $color_background;
	}
	
	public function getText(): string
	{
		return $this->text;
	}
	
	public function setText( string $text ): void
	{
		$this->text = $text;
	}
	
	public function setImageProductDetail( string $image ) : void
	{
		$this->image_pictogram_product_detail = $image;
	}
	
	public function getImageProductDetail() : string
	{
		return $this->image_pictogram_product_detail;
	}
	
	public function getImageProductDetailThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl('pictogram_product_detail', $max_w, $max_h);
	}
	
	
	public function setImageProductListing( string $image ) : void
	{
		$this->image_pictogram_product_listing = $image;
	}
	
	public function getImageProductListing() : string
	{
		return $this->image_pictogram_product_listing;
	}
	
	public function getImageProductListingThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl('pictogram_product_listing', $max_w, $max_h);
	}
	
	
	/**
	 * @var static[]|null
	 */
	protected static ?array $active_stickers = null;
	
	/**
	 * @return static[]
	 */
	public static function getProductStickers( int $product_id ) : array
	{
		if(static::$active_stickers===null) {
			static::$active_stickers = static::getAllActive( EShops::getCurrent(), ['priority'] );
		}
		
		$stickers = [];
		
		foreach(static::$active_stickers as $sticker) {
			if( $sticker->isRelevant( [$product_id] ) ) {
				$stickers[] = $sticker;
			}
		}

		return $stickers;
	}

}