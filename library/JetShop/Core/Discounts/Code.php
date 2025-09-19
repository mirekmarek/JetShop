<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use Jet\Data_DateTime;

use JetApplication\CashDesk;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Application_Service_Admin_DiscountCodesDefinition;
use JetApplication\Discounts;
use JetApplication\Discounts_Code;
use JetApplication\Application_Service_EShop_DiscountModule_Code;
use JetApplication\EShopEntity_Marketing;
use JetApplication\EShopEntity_Definition;
use JetApplication\Order;
use JetApplication\Pricelists;
use JetApplication\Application_Service_EShop;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Discounts_Code_Usage;

use JetApplication\Discounts_Discount;

/**
 *
 */
#[DataModel_Definition(
	name: 'discounts_code',
	database_table_name: 'discounts_codes',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Discount code',
	admin_manager_interface: Application_Service_Admin_DiscountCodesDefinition::class
)]
class Core_Discounts_Code extends EShopEntity_Marketing implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
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

	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Do not combine the code with other codes'
	)]
	protected bool $do_not_combine = false;
	
	public static function codeExists( string $code, int $skip_id=0 ) : bool
	{
		return (bool)static::dataFetchCol(['id'], [
			'code'=>$code,
			'AND',
			'id !=' => $skip_id
		]);
	}
	
	public static function getByCode( string $discount_code, ?EShop $eshop=null ): ?static
	{
		if(!$eshop) {
			$eshop = EShops::getCurrent();
		}
		
		$discount_code = strtolower($discount_code);


		$codes = static::fetch([
			'discounts_code' => [
				'code' => $discount_code,
				'AND',
				$eshop->getWhere()
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

	public function setCode( string $value ) : void
	{
		$value = strtolower($value);
		$this->code = $value;
	}
	
	public function getCode() : string
	{
		return $this->code;
	}

	
	public function setMinimalOrderAmount( float $value ) : void
	{
		$this->minimal_order_amount = $value;
	}
	
	public function getMinimalOrderAmount() : float
	{
		return $this->minimal_order_amount;
	}
	
	public function setNumberOfCodesAvailable( int $value ) : void
	{
		$this->number_of_codes_available = $value;
	}
	
	public function getNumberOfCodesAvailable() : int
	{
		return $this->number_of_codes_available;
	}
	
	public function setNumberOfCodesUsed( int $value ) : void
	{
		$this->number_of_codes_used = $value;
	}
	
	public function getNumberOfCodesUsed() : int
	{
		return $this->number_of_codes_used;
	}

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
	
	
	public function getRelevantProductAmount( CashDesk $cash_desk ) : float
	{
		$amount = 0;
		
		foreach( $cash_desk->getCart()->getItems() as $item) {
			if( $this->isRelevant([ $item->getProductId() ]) ) {
				$amount += $item->getProduct()->getPrice( Pricelists::getCurrent() ) * $item->getNumberOfUnits();
			}
		}
		
		return $amount;
	}

	public function isValid( CashDesk $cash_desk, ?string &$error_code='', ?array &$error_data=[] ) : bool
	{
		$error_data = [];
		
		$now = Data_DateTime::now();

		$valid_from = $this->getActiveFrom();
		$valid_till = $this->getActiveTill();

		if(
			$valid_from &&
			$valid_from>$now
		) {
			$error_code = 'future_code';
			return false;
		}

		if(
			$valid_till &&
			$valid_till<$now
		) {
			$error_code = 'past_code';
			return false;
		}
		
		
		if($this->getDoNotCombine()) {
			/**
			 * @var Application_Service_EShop_DiscountModule_Code $module
			 */
			$module = static::getModule( $cash_desk->getEshop() );
			
			$used_coupons = $module?->getUsedCodesRaw()??[];
			
			$i = array_search( $this->getId(), $used_coupons );
			if($i!==false) {
				unset($used_coupons[$i]);
			}
			
			if( $used_coupons ) {
				$error_code = 'do_not_combine';
				
				return false;
			}
		}
		

		if($this->getNumberOfCodesUsed()>=$this->getNumberOfCodesAvailable()) {
			$error_code = 'used';
			return false;
		}
		
		
		$has_some_relevant_product = false;
		
		foreach( $cash_desk->getCart()->getItems() as $item ) {
			if( $this->isRelevant( [$item->getProductId() ] ) ) {
				$has_some_relevant_product = true;
				break;
			}
		}
		
		
		if( !$has_some_relevant_product ) {
			$error_code = 'no_allowed_products_in_cart';
			
			return false;
		}
		

		if( $this->getMinimalOrderAmount()>0 ) {
			
			$amount = $this->getRelevantProductAmount( $cash_desk );
			
			if($amount<$this->getMinimalOrderAmount()) {
				$error_code = 'under_min_value';
				$error_data = [
					'MIN' => Application_Service_EShop::PriceFormatter()->formatWithCurrency( $this->getMinimalOrderAmount() )
				];
				
				return false;
			}
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
	
	public function getAdminTitle(): string
	{
		return $this->getCode();
	}

	
	public function getDoNotCombine(): bool
	{
		return $this->do_not_combine;
	}
	
	public function setDoNotCombine( bool $do_not_combine ): void
	{
		$this->do_not_combine = $do_not_combine;
	}
	
	
	protected function setupAddForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}
	
	
	protected function setupForm( Form $form ) : void
	{
		$eshop = new Form_Field_Select('eshop', 'e-shop');
		$eshop->setSelectOptions( EShops::getScope() );
		$eshop->setDefaultValue( $this->getEshop()->getKey() );
		$eshop->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$eshop->setFieldValueCatcher( function( string $eshop_key ) {
			$eshop = EShops::get( $eshop_key );
			$this->setEshop( $eshop );
		} );
		
		$form->addField( $eshop );
		
		$code = $form->getField('code');
		
		$code->setValidator(function( Form_Field_Input $field ) {
			$value = $field->getValue();
			if($value==='') {
				return true;
			}
			
			if(static::codeExists($value, $this->getId())) {
				$field->setError('code_exists');
				
				return false;
			}
			
			return true;
		});
		
	}
	
	public static function getModule( ?EShop $eshop=null ) :null|Application_Module|Application_Service_EShop_DiscountModule_Code
	{
		return Discounts::Manager()->getActiveModuleByInterface( Application_Service_EShop_DiscountModule_Code::class, $eshop );
	}
	
}
