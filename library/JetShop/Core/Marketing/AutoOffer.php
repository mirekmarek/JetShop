<?php
namespace JetShop;

use Jet\DataModel;

use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\Entity_Admin_Interface;
use JetApplication\Entity_Admin_Trait;
use JetApplication\Admin_Managers_MarketingAutoOffers;
use JetApplication\Entity_HasImages_Interface;
use JetApplication\Entity_HasImages_Trait;
use JetApplication\Entity_Marketing;
use JetApplication\Entity_Definition;
use JetApplication\Marketing_AutoOffer;

#[DataModel_Definition(
	name: 'auto_offers',
	database_table_name: 'auto_offers',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_MarketingAutoOffers::class,
	images: [
		'main' => 'Main image',
		'pictogram' => 'Pictogram image',
	]
)]
abstract class Core_Marketing_AutoOffer extends Entity_Marketing implements
	Entity_HasImages_Interface,
	Entity_Admin_Interface
{
	use Entity_HasImages_Trait;
	use Entity_Admin_Trait;
	
	public const SHOW_MODE_NORMAL = 'normal';
	public const SHOW_MODE_NO_LINK = 'no_link';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN,
	)]
	protected int $offer_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:',
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Label:',
	)]
	protected string $label = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 99999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Description:',
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_main = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_pictogram = '';
	

	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Show mode:',
		select_options_creator: [
			Marketing_AutoOffer::class,
			'getShowModeScope'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Invalid value',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected string $show_mode = self::SHOW_MODE_NORMAL;

	
	
	public static function getShowModeScope() : array
	{
		return [
			Marketing_AutoOffer::SHOW_MODE_NORMAL  => Tr::_('Normal'),
			Marketing_AutoOffer::SHOW_MODE_NO_LINK => Tr::_('No link')
		];
	}
	
	public function getOfferProductId(): int
	{
		return $this->offer_product_id;
	}
	
	public function setOfferProductId( int $offer_product_id ): void
	{
		$this->offer_product_id = $offer_product_id;
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
	
	
	public function getShowMode(): string
	{
		return $this->show_mode;
	}
	
	public function setShowMode( string $show_mode ): void
	{
		$this->show_mode = $show_mode;
	}
	
	public function getLabel(): string
	{
		return $this->label;
	}
	
	public function setLabel( string $label ): void
	{
		$this->label = $label;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ): void
	{
		$this->description = $description;
	}
	
	
	public function setImageMain( string $image_main ) : void
	{
		$this->image_main = $image_main;
	}
	
	public function getImageMain() : string
	{
		return $this->image_main;
	}
	
	public function getImageMainThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl('main', $max_w, $max_h);
	}
	
	public function getMainImageUrl(): string
	{
		return $this->getImageUrl('main');
	}
	
	
	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->image_pictogram = $image_pictogram;
	}
	
	public function getImagePictogram() : string
	{
		return $this->image_pictogram;
	}
	
	
	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl('pictogram', $max_w, $max_h);
	}
	
	public function getPictogramImageUrl(): string
	{
		return $this->getImageUrl('pictogram');
	}
	
	public function isRelevant( array $product_ids ) : bool
	{
		if(in_array($this->offer_product_id, $product_ids)) {
			return false;
		}
		
		return parent::isRelevant( $product_ids );
	}
}