<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use JetApplication\Delivery_Method;
use JetApplication\Entity_WithCodeAndShopData_ShopData;

#[DataModel_Definition(
	name: 'delivery_method_shop_data',
	database_table_name: 'delivery_methods_shop_data',
	parent_model_class: Delivery_Method::class
)]
abstract class Core_Delivery_Method_ShopData extends Entity_WithCodeAndShopData_ShopData
{

	public const  IMG_ICON1 = 'icon1';
	public const  IMG_ICON2 = 'icon2';
	public const  IMG_ICON3 = 'icon3';

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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	protected string $title = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 99999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Description:'
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Short description:'
	)]
	protected string $description_short = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Confirmation e-mail info text:'
	)]
	protected string $confirmation_email_info_text = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:'
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Default price:'
	)]
	protected float $default_price = 0.0;
	
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

	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Discount is not allowed'
	)]
	protected bool $discount_is_not_allowed = false;
	

	public function setIcon1( string $image ) : void
	{
		$this->image_icon1 = $image;
	}

	public function getIcon1() : string
	{
		return $this->image_icon1;
	}
	
	public function setIcon2( string $image ) : void
	{
		$this->image_icon2 = $image;
	}
	
	public function getIcon2() : string
	{
		return $this->image_icon2;
	}
	
	
	public function setIcon3( string $image ) : void
	{
		$this->image_icon3 = $image;
	}
	
	public function getIcon3() : string
	{
		return $this->image_icon3;
	}
	
	public function setTitle( string $value ) : void
	{
		$this->title = $value;
	}
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function setDescription( string $value ) : void
	{
		$this->description = $value;
	}
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	public function setDescriptionShort( string $value ) : void
	{
		$this->description_short = $value;
	}
	
	public function getDescriptionShort() : string
	{
		return $this->description_short;
	}
	
	public function getConfirmationEmailInfoText(): string
	{
		return $this->confirmation_email_info_text;
	}

	public function setConfirmationEmailInfoText( string $confirmation_email_info_text ): void
	{
		$this->confirmation_email_info_text = $confirmation_email_info_text;
	}
	
	public function setPriority( int $value ) : void
	{
		$this->priority = $value;
	}
	
	public function getPriority() : int
	{
		return $this->priority;
	}
	
	public function setDefaultPrice( float $value ) : void
	{
		$this->default_price = $value;
	}

	public function getDefaultPrice() : float
	{
		return $this->default_price;
	}

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

	public function setDiscountIsNotAllowed( bool $value ) : void
	{
		$this->discount_is_not_allowed = $value;
	}
	
	public function getDiscountIsNotAllowed() : bool
	{
		return $this->discount_is_not_allowed;
	}

}
