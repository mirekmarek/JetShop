<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Tr;

use JetApplication\Delivery_Deadline;
use JetApplication\CommonEntity_ShopData;
use JetApplication\Images_ShopDataInterface;
use JetApplication\Images_ShopDataTrait;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Delivery_Method;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_deadline_shop_data',
	database_table_name: 'delivery_deadlines_shop_data',
	parent_model_class: Delivery_Deadline::class,
)]
abstract class Core_Delivery_Deadline_ShopData extends CommonEntity_ShopData implements Images_ShopDataInterface
{
	use Images_ShopDataTrait;

	const IMG_ICON1 = 'icon1';
	const IMG_ICON2 = 'icon2';
	const IMG_ICON3 = 'icon3';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		related_to: 'main.code',
		is_key: true,
	)]
	protected string $delivery_deadline_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon2 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon3 = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	protected string $title = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 99999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Description:'
	)]
	protected string $description = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Short description:'
	)]
	protected string $description_short = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Delay business days - optimistic:'
	)]
	protected int $delay_business_days_optimistic = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Delay business days - pessimistic:'
	)]
	protected int $delay_business_days_pessimistic = 0;



	/**
	 * @param string $value
	 */
	public function setDeliveryDeadlineCode( string $value ) : void
	{
		$this->delivery_deadline_code = $value;
	}

	/**
	 * @return string
	 */
	public function getDeliveryDeadlineCode() : string
	{
		return $this->delivery_deadline_code;
	}

	public function getImageEntity(): string
	{
		return 'delivery_method';
	}

	public function getImageObjectId(): int|string
	{
		return $this->getDeliveryDeadlineCode();
	}

	public static function getImageClasses(): array
	{
		return [
			Delivery_Method_ShopData::IMG_ICON1 => Tr::_('Icon 1', [], Delivery_Method::getManageModuleName() ),
			Delivery_Method_ShopData::IMG_ICON2 => Tr::_('Icon 2', [], Delivery_Method::getManageModuleName() ),
			Delivery_Method_ShopData::IMG_ICON3 => Tr::_('Icon 3', [], Delivery_Method::getManageModuleName() ),
		];
	}

	public function setIcon1( string $image ) : void
	{
		$this->setImage( Delivery_Method_ShopData::IMG_ICON1, $image );
	}

	public function getIcon1() : string
	{
		return $this->getImage( Delivery_Method_ShopData::IMG_ICON1 );
	}

	public function getIcon1Url() : string
	{
		return $this->getImageUrl( Delivery_Method_ShopData::IMG_ICON1 );
	}

	public function getIcon1ThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Delivery_Method_ShopData::IMG_ICON1, $max_w, $max_h );
	}

	public function setIcon2( string $image ) : void
	{
		$this->setImage( Delivery_Method_ShopData::IMG_ICON2, $image );
	}

	public function getIcon2() : string
	{
		return $this->getImage( Delivery_Method_ShopData::IMG_ICON2 );
	}

	public function getIcon2Url() : string
	{
		return $this->getImageUrl( Delivery_Method_ShopData::IMG_ICON2 );
	}

	public function getIcon2ThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Delivery_Method_ShopData::IMG_ICON2, $max_w, $max_h );
	}


	public function setIcon3( string $image ) : void
	{
		$this->setImage( Delivery_Method_ShopData::IMG_ICON3, $image );
	}

	public function getIcon3() : string
	{
		return $this->getImage( Delivery_Method_ShopData::IMG_ICON3 );
	}

	public function getIcon3Url() : string
	{
		return $this->getImageUrl( Delivery_Method_ShopData::IMG_ICON3 );
	}

	public function getIcon3ThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Delivery_Method_ShopData::IMG_ICON3, $max_w, $max_h );
	}

	/**
	 * @param string $value
	 */
	public function setTitle( string $value ) : void
	{
		$this->title = $value;
	}

	/**
	 * @return string
	 */
	public function getTitle() : string
	{
		return $this->title;
	}

	/**
	 * @param string $value
	 */
	public function setDescription( string $value ) : void
	{
		$this->description = $value;
	}

	/**
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}

	/**
	 * @param string $value
	 */
	public function setDescriptionShort( string $value ) : void
	{
		$this->description_short = $value;
	}

	/**
	 * @return string
	 */
	public function getDescriptionShort() : string
	{
		return $this->description_short;
	}

	/**
	 * @param int $value
	 */
	public function setDelayBusinessDaysOptimistic( int $value ) : void
	{
		$this->delay_business_days_optimistic = $value;
	}

	/**
	 * @return int
	 */
	public function getDelayBusinessDaysOptimistic() : int
	{
		return $this->delay_business_days_optimistic;
	}

	/**
	 * @param int $value
	 */
	public function setDelayBusinessDaysPessimistic( int $value ) : void
	{
		$this->delay_business_days_pessimistic = $value;
	}

	/**
	 * @return int
	 */
	public function getDelayBusinessDaysPessimistic() : int
	{
		return $this->delay_business_days_pessimistic;
	}

}
