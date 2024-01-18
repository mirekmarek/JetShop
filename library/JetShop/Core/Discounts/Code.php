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
use Jet\Data_DateTime;
use Jet\Form_Field_DateTime;
use Jet\Tr;

use JetApplication\Discounts_Code;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Order;
use JetApplication\Shop_Managers;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Discounts_Code_Usage;

use JetApplication\Discounts_Discount;

/**
 *
 */
#[DataModel_Definition(
	name: 'discounts_code',
	database_table_name: 'discounts_codes',
)]
class Core_Discounts_Code extends Entity_WithShopRelation
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Code:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
			'code_exists' => 'Code already exists'
		]
	)]
	protected string $code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 99999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal description:'
	)]
	protected string $internal_description = '';
	
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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Minimal order amount:'
	)]
	protected float $minimal_order_amount = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Number of codes available:'
	)]
	protected int $number_of_codes_available = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $number_of_codes_used = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		select_options_creator: [
			Discounts_Discount::class,
			'getDiscountTypeScope'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY         => 'Please select discount type',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select discount type'
		]
		
	)]
	protected string $discount_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Discount amount:'
	)]
	protected float $discount = 0.0;
	
	public static function codeExists( string $code, int $skip_id=0 ) : bool
	{
		return (bool)static::dataFetchCol(['id'], [
			'code'=>$code,
			'AND',
			'id !=' => $skip_id
		]);
	}

	
	

	/**
	 * @param int|string $id
	 * @return static|null
	 */
	public static function get( int|string $id ): static|null
	{
		return static::load( $id );
	}

	public static function getByCode( string $discount_code, ?Shops_Shop $shop=null ): ?static
	{
		if(!$shop) {
			$shop = Shops::getCurrent();
		}
		
		$discount_code = strtolower($discount_code);


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
		$value = strtolower($value);
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
	
	public function getDiscountPercentageMtp() : float
	{
		return -1*round( $this->discount / 100, 2);
	}

	public function isValid( ?string &$error_code='', ?array &$error_data=[] ) : bool
	{
		$error_data = [];
		
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
			Shop_Managers::ShoppingCart()->getCart()->getAmount()<$this->getMinimalOrderAmount()
		) {
			$error_code = 'under_min_value';
			$error_data = [
				'MIN' => Shop_Managers::PriceFormatter()->formatWithCurrency( $this->getMinimalOrderAmount() )
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
