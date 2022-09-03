<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field_Select;
use Jet\Tr;
use Jet\Form_Field;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_method_shop_data',
	database_table_name: 'delivery_methods_shop_data',
	parent_model_class: Delivery_Method::class
)]
abstract class Core_Delivery_Method_ShopData extends CommonEntity_ShopData implements Images_ShopDataInterface
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
	protected string $delivery_method_code = '';

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
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Confirmation e-mail info text:'
	)]
	protected string $confirmation_email_info_text = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:'
	)]
	protected int $priority = 0;

	/**
	 * @var float
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Default price:'
	)]
	protected float $default_price = 0.0;

	/**
	 * @var float
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'VAT rate:',
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select VAT rate',
		],
		creator: ['this', 'createVatRateInputField'],
	)]
	protected float $vat_rate = 0.0;

	
	
	/**
	 * @var bool
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Discount is not allowed'
	)]
	protected bool $discount_is_not_allowed = false;


	/**
	 * @param string $value
	 */
	public function setDeliveryMethodCode( string $value ) : void
	{
		$this->delivery_method_code = $value;
	}

	/**
	 * @return string
	 */
	public function getDeliveryMethodCode() : string
	{
		return $this->delivery_method_code;
	}

	public function getImageEntity(): string
	{
		return 'delivery_method';
	}

	public function getImageObjectId(): int|string
	{
		return $this->getDeliveryMethodCode();
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
	 * @return string
	 */
	public function getConfirmationEmailInfoText(): string
	{
		return $this->confirmation_email_info_text;
	}

	/**
	 * @param string $confirmation_email_info_text
	 */
	public function setConfirmationEmailInfoText( string $confirmation_email_info_text ): void
	{
		$this->confirmation_email_info_text = $confirmation_email_info_text;
	}


	/**
	 * @param int $value
	 */
	public function setPriority( int $value ) : void
	{
		$this->priority = $value;
	}

	/**
	 * @return int
	 */
	public function getPriority() : int
	{
		return $this->priority;
	}

	/**
	 * @param float $value
	 */
	public function setDefaultPrice( float $value ) : void
	{
		$this->default_price = $value;
	}

	/**
	 * @return float
	 */
	public function getDefaultPrice() : float
	{
		return $this->default_price;
	}

	/**
	 * @param float $value
	 */
	public function setVatRate( float $value ) : void
	{
		$this->vat_rate = $value;
	}

	/**
	 * @return float
	 */
	public function getVatRate() : float
	{
		return $this->vat_rate;
	}

	public function createVatRateInputField( Form_Field_Select $input ) : Form_Field_Select
	{
		$shop = $this->getShop();
		
		$input->setDefaultValue( !$this->getIsSaved() ? $shop->getDefaultVatRate()  : $this->vat_rate );


		$vat_rates = $shop->getVatRates();

		$vat_rates = array_combine($vat_rates, $vat_rates);

		$input->setSelectOptions($vat_rates);

		return $input;
	}

	/**
	 * @param bool $value
	 */
	public function setDiscountIsNotAllowed( bool $value ) : void
	{
		$this->discount_is_not_allowed = $value;
	}

	/**
	 * @return bool
	 */
	public function getDiscountIsNotAllowed() : bool
	{
		return $this->discount_is_not_allowed;
	}

}
