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
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Services_Kind;
use JetApplication\Services_Service;

/**
 *
 */
#[DataModel_Definition(
	name: 'services_shop_data',
	database_table_name: 'services_shop_data',
	parent_model_class: Services_Service::class,
)]
abstract class Core_Services_Service_ShopData extends Entity_WithShopData_ShopData
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $kind = '';

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
		type: Form_Field::TYPE_FLOAT,
		label: 'VAT rate:',
		creator: ['this','createVatRateInputField']
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
	
	public function setKind( string $code ): void
	{
		$this->kind = $code;
	}
	
	public function getKindCode(): string
	{
		return $this->kind;
	}
	
	public function getKind() : ?Services_Kind
	{
		return Services_Kind::get( $this->kind );
	}
	
	public function getKindTitle() : string
	{
		$kind = $this->getKind();
		return $kind ? $kind->getTitle() : '';
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
	
	public function getVatRate() : float
	{
		return $this->vat_rate;
	}

	public function createVatRateInputField() : Form_Field_Select
	{
		$shop = $this->getShop();

		$input = new Form_Field_Select('vat_rate', 'VAT rate:' );
		$input->setDefaultValue( !$this->getIsSaved() ? $shop->getDefaultVatRate()  : $this->vat_rate );

		$input->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select VAT rate',
		]);

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
	
	public function getImageIcon1(): string
	{
		return $this->image_icon1;
	}
	
	public function setImageIcon1( string $image_icon1 ): void
	{
		$this->image_icon1 = $image_icon1;
	}
	
	public function getImageIcon2(): string
	{
		return $this->image_icon2;
	}
	
	public function setImageIcon2( string $image_icon2 ): void
	{
		$this->image_icon2 = $image_icon2;
	}
	
	public function getImageIcon3(): string
	{
		return $this->image_icon3;
	}
	
	public function setImageIcon3( string $image_icon3 ): void
	{
		$this->image_icon3 = $image_icon3;
	}
	
	
}
