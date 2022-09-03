<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use Jet\Data_DateTime;
use Jet\Form_Field_DateTime;
use Jet\Tr;

/**
 *
 */
#[DataModel_Definition(
	name: 'discounts_code',
	database_table_name: 'discounts_codes',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
class Core_Discounts_Code extends DataModel
{
	const DISCOUNT_TYPE_PRODUCTS_PERCENTAGE = 'products_percentage';
	const DISCOUNT_TYPE_PRODUCTS_AMOUNT = 'products_amount';

	const DISCOUNT_TYPE_DELIVERY_PERCENTAGE = 'delivery_percentage';
	const DISCOUNT_TYPE_DELIVERY_AMOUNT = 'delivery_amount';

	const DISCOUNT_TYPE_PAYMENT_PERCENTAGE = 'payment_percentage';
	const DISCOUNT_TYPE_PAYMENT_AMOUNT = 'payment_amount';
	
	const DISCOUNT_TYPE_ORDER_PERCENTAGE = 'order_percentage';
	const DISCOUNT_TYPE_ORDER_AMOUNT = 'order_amount';

	use CommonEntity_ShopRelationTrait;

	/**
	 * @var ?Form
	 */
	protected ?Form $_form_edit = null;

	/**
	 * @var ?Form
	 */
	protected ?Form $_form_add = null;

	/**
	 * @var int
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Code:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter code'
		]
	)]
	protected string $code = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 99999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal description:'
	)]
	protected string $internal_description = '';

	/**
	 * @var ?Data_DateTime
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE_TIME,
		label: 'Valid from:',
		error_messages: [
			Form_Field_DateTime::ERROR_CODE_EMPTY          => 'Please enter date and time',
			Form_Field_DateTime::ERROR_CODE_INVALID_FORMAT => 'Please enter date and time'
		]
	)]
	protected ?Data_DateTime $valid_from = null;

	/**
	 * @var ?Data_DateTime
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE_TIME,
		label: 'Valid till:',
		error_messages: [
			Form_Field_DateTime::ERROR_CODE_EMPTY          => 'Please enter date and time',
			Form_Field_DateTime::ERROR_CODE_INVALID_FORMAT => 'Please enter date and time'
		]
	)]
	protected ?Data_DateTime $valid_till = null;

	/**
	 * @var float
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Minimal order amount:'
	)]
	protected float $minimal_order_amount = 0.0;

	/**
	 * @var int
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Number of codes available:'
	)]
	protected int $number_of_codes_available = 0;

	/**
	 * @var int
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $number_of_codes_used = 0;

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		select_options_creator: [
			Discounts_Code::class,
			'getDiscountTypeScope'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY         => 'Please select discount type',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select discount type'
		]
		
	)]
	protected string $discount_type = '';

	/**
	 * @var float
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Discount amount:'
	)]
	protected float $discount = 0.0;


	public static function getDiscountTypeScope(): array
	{
		return [
			self::DISCOUNT_TYPE_PRODUCTS_PERCENTAGE => Tr::_( 'products -% discount' ),
			self::DISCOUNT_TYPE_PRODUCTS_AMOUNT     => Tr::_( 'products - amount' ),

			self::DISCOUNT_TYPE_DELIVERY_PERCENTAGE => Tr::_( 'delivery - % discount' ),
			self::DISCOUNT_TYPE_DELIVERY_AMOUNT     => Tr::_( 'delivery - amount' ),

			self::DISCOUNT_TYPE_PAYMENT_PERCENTAGE => Tr::_( 'payment - % discount' ),
			self::DISCOUNT_TYPE_PAYMENT_AMOUNT     => Tr::_( 'payment - amount' ),
			
			self::DISCOUNT_TYPE_ORDER_PERCENTAGE => Tr::_( 'order- % discount' ),
			self::DISCOUNT_TYPE_ORDER_AMOUNT     => Tr::_( 'order - amount' ),
		];
	}

	/**
	 * @return Form
	 */
	public function getEditForm(): Form
	{
		if( !$this->_form_edit ) {
			$this->_form_edit = $this->createForm( 'edit_form' );
		}

		return $this->_form_edit;
	}

	/**
	 * @return bool
	 */
	public function catchEditForm(): bool
	{
		return $this->getEditForm()->catch();
	}

	/**
	 * @return Form
	 */
	public function getAddForm(): Form
	{
		if( !$this->_form_add ) {
			$this->_form_add = $this->createForm( 'add_form' );
		}

		return $this->_form_add;
	}

	/**
	 * @return bool
	 */
	public function catchAddForm(): bool
	{
		return $this->getAddForm()->catch();
	}

	/**
	 * @param int|string $id
	 * @return static|null
	 */
	public static function get( int|string $id ): static|null
	{
		return static::load( $id );
	}

	public static function getByCode( string $discount_code, ?Shops_Shop $shop=null ): static|null
	{
		if(!$shop) {
			$shop = Shops::getCurrent();
		}

		/**
		 * @var Discounts_Code[] $codes
		 */
		$codes = static::fetch([
			'discounts_code' => [
				'code' => $discount_code,
				'AND',
				$shop->getWhere()
			]
		]);

		if(count($codes)!=1) {
			return null;
		}

		return $codes[0];

	}

	/**
	 * @return static[]
	 */
	public static function getList() : iterable
	{
		$where = [];

		$list = static::fetchInstances( $where );

		return $list;
	}


	public function getId() : int
	{
		return $this->id;
	}

	/**
	 * @param string $value
	 */
	public function setCode( string $value ) : void
	{
		$this->code = $value;
	}

	/**
	 * @return string
	 */
	public function getCode() : string
	{
		return $this->code;
	}

	/**
	 * @param string $value
	 */
	public function setInternalDescription( string $value ) : void
	{
		$this->internal_description = $value;
	}

	/**
	 * @return string
	 */
	public function getInternalDescription() : string
	{
		return $this->internal_description;
	}

	/**
	 * @param Data_DateTime|string|null $value
	 */
	public function setValidFrom( Data_DateTime|string|null $value ) : void
	{
		if( $value===null ) {
			$this->valid_from = null;
			return;
		}
		
		if( !( $value instanceof Data_DateTime ) ) {
			$value = new Data_DateTime( (string)$value );
		}
		
		$this->valid_from = $value;
	}

	/**
	 * @return Data_DateTime|null
	 */
	public function getValidFrom() : Data_DateTime|null
	{
		return $this->valid_from;
	}

	/**
	 * @param Data_DateTime|string|null $value
	 */
	public function setValidTill( Data_DateTime|string|null $value ) : void
	{
		if( $value===null ) {
			$this->valid_till = null;
			return;
		}
		
		if( !( $value instanceof Data_DateTime ) ) {
			$value = new Data_DateTime( (string)$value );
		}
		
		$this->valid_till = $value;
	}

	/**
	 * @return Data_DateTime|null
	 */
	public function getValidTill() : Data_DateTime|null
	{
		return $this->valid_till;
	}

	/**
	 * @param float $value
	 */
	public function setMinimalOrderAmount( float $value ) : void
	{
		$this->minimal_order_amount = $value;
	}

	/**
	 * @return float
	 */
	public function getMinimalOrderAmount() : float
	{
		return $this->minimal_order_amount;
	}

	/**
	 * @param int $value
	 */
	public function setNumberOfCodesAvailable( int $value ) : void
	{
		$this->number_of_codes_available = $value;
	}

	/**
	 * @return int
	 */
	public function getNumberOfCodesAvailable() : int
	{
		return $this->number_of_codes_available;
	}

	/**
	 * @param int $value
	 */
	public function setNumberOfCodesUsed( int $value ) : void
	{
		$this->number_of_codes_used = $value;
	}

	/**
	 * @return int
	 */
	public function getNumberOfCodesUsed() : int
	{
		return $this->number_of_codes_used;
	}

	/**
	 * @param string $value
	 */
	public function setDiscountType( string $value ) : void
	{
		$this->discount_type = $value;
	}

	/**
	 * @return string
	 */
	public function getDiscountType() : string
	{
		return $this->discount_type;
	}

	/**
	 * @param float $value
	 */
	public function setDiscount( float $value ) : void
	{
		$this->discount = $value;
	}

	/**
	 * @return float
	 */
	public function getDiscount() : float
	{
		return $this->discount;
	}

	public function isValid( ?string &$error_code='', ?array &$error_data=[] ) : bool
	{
		$now = Data_DateTime::now();

		$valid_from = $this->getValidFrom();
		$valid_till = $this->getValidTill();

		if(
			$valid_from &&
			$valid_from>$now
		) {
			$error_message = Tr::_('The discount code will not be active until the future');
			$error_code = 'future_code';
			return false;
		}

		if(
			$valid_till &&
			$valid_till<$now
		) {
			$error_message = Tr::_('The discount code is no longer valid');
			$error_code = 'past_code';
			return false;
		}

		if($this->getNumberOfCodesUsed()>=$this->getNumberOfCodesAvailable()) {
			$error_message = Tr::_('The discount code has already been used');
			$error_code = 'used';
			return false;
		}

		if(
			$this->getMinimalOrderAmount()>0 &&
			ShoppingCart::get()->getAmount()<$this->getMinimalOrderAmount()
		) {
			$error_code = 'under_min_value';
			$error_data = [
				'MIN' => Price::formatWithCurrency( $this->getMinimalOrderAmount() )
			];

			return false;
		}


		return true;
	}

	public function used( Order $order ) : void
	{
		$used = new Discounts_Code_Usage();

		$used->setOrderId( $order->getId() );
		$used->setCodeId( $this->getId() );
		$used->setDateTime( Data_DateTime::now() );

		$used->save();
		$this->updateUsedCount();
	}

	public static function cancelUsages( Order $order ) : void
	{
		/**
		 * @var Discounts_Code_Usage[] $usages
		 */
		$usages = Discounts_Code_Usage::fetchInstances([
			'order_id' => $order->getId(),
			'AND',
			'cancelled' => false
		]);


		$code_ids = [];
		foreach($usages as $u) {
			$u->setCancelled(true);
			$u->setCancelledDateTime( Data_DateTime::now() );
			$u->save();

			$code_ids[$u->getCodeId()] = $code_ids;
		}

		foreach($code_ids as $c_id) {
			$code = Discounts_Code::get( $c_id );
			$code->updateUsedCount();
		}
	}

	public function cancelUsage( Order $order ) : void
	{
		/**
		 * @var Discounts_Code_Usage[] $usages
		 */
		$usages = Discounts_Code_Usage::fetchInstances([
			'order_id' => $order->getId(),
			'AND',
			'code_id' => $this->id,
			'AND',
			'cancelled' => false
		]);

		foreach($usages as $u) {
			$u->setCancelled(true);
			$u->setCancelledDateTime( Data_DateTime::now() );
			$u->save();
		}

		$this->updateUsedCount();
	}

	public function updateUsedCount() : void
	{
		$count = count(Discounts_Code_Usage::fetchIDs([
			'code_id' => $this->id,
			'AND',
			'cancelled' => false
		]));

		$this->number_of_codes_used = $count;

		$this->save();
	}
}
