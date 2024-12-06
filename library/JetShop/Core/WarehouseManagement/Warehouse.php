<?php
/**
 * 
 */

namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;

use JetApplication\Context;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\DataList;
use JetApplication\Entity_Common;
use JetApplication\Product;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_StockMovement;
use JetApplication\WarehouseManagement_StockMovement_Type;
use JetApplication\WarehouseManagement_Warehouse;


#[DataModel_Definition(
	name: 'warehouse',
	database_table_name: 'whm_warehouses',
)]
class Core_WarehouseManagement_Warehouse extends Entity_Common
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Currency:',
		is_required: false,
		select_options_creator: [
			Currencies::class,
			'getScope'
		],
		error_messages: [
		]
	)]
	protected string $currency_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is virtual'
	)]
	protected bool $is_virtual = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - name:'
	)]
	protected string $address_name = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - street and number:'
	)]
	protected string $address_street_no = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - town:'
	)]
	protected string $address_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - zip:'
	)]
	protected string $address_zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - country:'
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Address - country:',
		is_required: false,
		select_options_creator: [
			DataList::class,
			'countries'
		]
	)]
	protected string $address_country = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_EMAIL,
		label: 'Internal e-mail:',
		is_required: false,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'please enter e-mail'
		]
	)]
	protected string $internal_email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal phone:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $internal_phone = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_EMAIL,
		label: 'Public e-mail:',
		is_required: false,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'please enter e-mail'
		]
	)]
	protected string $public_email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Public phone:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $public_phone = '';
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	public function getCurrency() : Currency
	{
		return Currencies::get( $this->currency_code );
	}

	public function setIsVirtual( bool $value ) : void
	{
		$this->is_virtual = $value;
	}
	
	public function getIsVirtual() : bool
	{
		return $this->is_virtual;
	}

	
	public function getAddressName(): string
	{
		return $this->address_name;
	}
	
	public function setAddressName( string $address_name ): void
	{
		$this->address_name = $address_name;
	}
	
	

	public function setAddressStreetNo( string $value ) : void
	{
		$this->address_street_no = $value;
	}

	public function getAddressStreetNo() : string
	{
		return $this->address_street_no;
	}

	public function setAddressCountry( string $value ) : void
	{
		$this->address_country = $value;
	}
	
	public function getAddressCountry() : string
	{
		return $this->address_country;
	}

	public function setAddressTown( string $value ) : void
	{
		$this->address_town = $value;
	}

	public function getAddressTown() : string
	{
		return $this->address_town;
	}

	public function setAddressZip( string $value ) : void
	{
		$this->address_zip = $value;
	}

	public function getAddressZip() : string
	{
		return $this->address_zip;
	}
	
	public function setInternalEmail( string $value ) : void
	{
		$this->internal_email = $value;
	}
	
	public function getInternalEmail() : string
	{
		return $this->internal_email;
	}
	
	public function setInternalPhone( string $value ) : void
	{
		$this->internal_phone = $value;
	}
	
	public function getInternalPhone() : string
	{
		return $this->internal_phone;
	}
	
	public function setPublicEmail( string $value ) : void
	{
		$this->public_email = $value;
	}
	
	public function getPublicEmail() : string
	{
		return $this->public_email;
	}
	
	public function setPublicPhone( string $value ) : void
	{
		$this->public_phone = $value;
	}
	
	public function getPublicPhone() : string
	{
		return $this->public_phone;
	}

	
	public function getSectors() : array
	{
		$where = ['warehouse_id'=>$this->getId()];
		$where[] = 'AND';
		$where['sector !='] = '';
		
		$data = WarehouseManagement_StockCard::dataFetchCol(
			select: ['sector'],
			where: $where,
			order_by: 'sector',
			raw_mode: true
		);
		
		$data = array_unique( $data );
		return $data;
	}
	
	public function getRacks( ?string $sector=null ) : array
	{
		$where = ['warehouse_id'=>$this->getId()];
		if($sector) {
			$where[] = 'AND';
			$where['sector'] = $sector;
		}
		
		$where[] = 'AND';
		$where['rack !='] = '';
		
		
		$data = WarehouseManagement_StockCard::dataFetchCol(
			select: ['rack'],
			where: $where,
			order_by: 'sector',
			raw_mode: true
		);
		
		$data = array_unique( $data );
		return $data;
	}
	
	public function getPositions(?string $sector=null, ?string $rack=null) : array
	{
		$where = ['warehouse_id'=>$this->getId()];
		if($sector) {
			$where[] = 'AND';
			$where['sector'] = $sector;
		}
		if($rack) {
			$where[] = 'AND';
			$where['rack'] = $rack;
		}
		
		$where[] = 'AND';
		$where['position !='] = '';
		
		
		$data = WarehouseManagement_StockCard::dataFetchCol(
			select: ['position'],
			where: $where,
			order_by: 'sector',
			raw_mode: true
		);
		
		$data = array_unique( $data );
		return $data;
	}
	
	
	protected array $cards = [];

	public function getCard( int $product_id ) : WarehouseManagement_StockCard
	{
		if(isset($this->cards[$product_id])) {
			return $this->cards[$product_id];
		}
		
		$card =  WarehouseManagement_StockCard::load( [
			'warehouse_id' => $this->getId(),
			'AND',
			'product_id' => $product_id
		
		]);
		
		if(!$card) {
			$card = new WarehouseManagement_StockCard();
			$card->setWarehouseId( $this->getId() );
			$card->setProductId( $product_id );
			$card->setPricePerUnit( 0.0, $this->getCurrency() );
			$card->save();
		}
		
		$this->cards[$product_id] = $card;
		
		return $card;
	}
	
	
	
	public function movement(
		WarehouseManagement_StockMovement_Type $type,
		int                                    $product_id,
		float                                  $number_of_units,
		Context                                $context,
		?Currency                              $currency = null,
		?float                                 $price_per_unit = null,
		string                                 $sector = '',
		string                                 $rack = '',
		string                                 $position = ''
	): WarehouseManagement_StockMovement
	{
		/**
		 * @var WarehouseManagement_Warehouse $this
		 */
		

		$movement = new WarehouseManagement_StockMovement();
		$movement->setDateTime( Data_DateTime::now() );
		
		$movement->setType( $type );
		$movement->setWarehouse( $this );
		
		$movement->setProductId( $product_id );
		
		$movement->setNumberOfUnits( $number_of_units, Product::getProductMeasureUnit($product_id) );
		
		$movement->setContext( $context );
		
		$movement->setSector( $sector );
		$movement->setRack( $rack );
		$movement->setPosition( $position );
		
		if($currency) {
			$movement->setPricePerUnit(
				$currency,
				$price_per_unit
			);
		}
		
		
		$movement->save();
		
		$wh_card = $this->getCard( $product_id );
		$wh_card->calc( $movement );
		$wh_card->save();
		
		
		return $movement;
	}
	
	
	/**
	 * @param Context $context
	 * @return WarehouseManagement_StockMovement[]
	 */
	public function getBlockingMovements( Context $context ) : array
	{
		$movements = WarehouseManagement_StockMovement::fetch([''=> [
			'warehouse_id' => $this->getId(),
			'AND',
			$context->getWhere(),
			'AND',
			'cancelled' => false,
			'AND',
			'type' => WarehouseManagement_StockMovement_Type::Blocking()
		]]);
		
		return $movements;
	}
	
	/**
	 * @param Context $context
	 * @return WarehouseManagement_StockMovement[]
	 */
	public function cancelBlocking( Context $context ) : array
	{
		$movements = $this->getBlockingMovements( $context );
		
		foreach($movements as $movement) {
			$movement->cancel();
		}
		
		return $movements;
	}
	
	public function unblock( int $product_id, float $number_of_units, Context $context ) : void
	{
		$blocking = WarehouseManagement_StockMovement::fetch([''=> [
			'warehouse_id' => $this->getId(),
			'AND',
			'type' => WarehouseManagement_StockMovement_Type::Blocking(),
			'AND',
			$context->getWhere(),
			'AND',
			'product_id' => $product_id,
			'AND',
			'cancelled' => false
		]]);
		
		
		$remains = $number_of_units;
		
		foreach($blocking as $b) {
			$b->cancel();
			
			$blocked_units = $b->getNumberOfUnits();
			$remains -= $blocked_units;
		}
		
		if($remains>0) {
			$this->movement(
				type: WarehouseManagement_StockMovement_Type::Blocking(),
				product_id: $product_id,
				number_of_units: $remains,
				context: $context
			);
		}
		
		
	}
	
	public function blocking(
		int $product_id,
		float $number_of_units,
		Context $context
	) : WarehouseManagement_StockMovement
	{
		return $this->movement(
			type: WarehouseManagement_StockMovement_Type::Blocking(),
			product_id: $product_id,
			number_of_units: $number_of_units,
			context: $context
		);
	}
	
	public function cancelMovement( int $product_id, Context $context, WarehouseManagement_StockMovement_Type $type ) : void
	{
		$movements = WarehouseManagement_StockMovement::fetch([''=> [
			'warehouse_id' => $this->getId(),
			'AND',
			'type' => $type,
			'AND',
			$context->getWhere(),
			'AND',
			'product_id' => $product_id,
			'AND',
			'cancelled' => false
		]]);
		
		
		foreach($movements as $b) {
			$b->cancel();
		}
		
	}
	
	
	public function out(
		int $product_id,
		float $number_of_units,
		Context $context
	) : WarehouseManagement_StockMovement
	{
		$wh_card = $this->getCard( $product_id );
		
		
		return $this->movement(
			type: WarehouseManagement_StockMovement_Type::Out(),
			product_id: $product_id,
			number_of_units: $number_of_units,
			context: $context,
			currency: $wh_card->getCurrency(),
			price_per_unit: $wh_card->getPricePerUnit()
		);
	}
	
	public function transferOut(
		int $product_id,
		float $number_of_units,
		Context $context
	) : WarehouseManagement_StockMovement
	{
		$wh_card = $this->getCard( $product_id );
		
		return $this->movement(
			type: WarehouseManagement_StockMovement_Type::TransferOut(),
			product_id: $product_id,
			number_of_units: $number_of_units,
			context: $context,
			currency: $wh_card->getCurrency(),
			price_per_unit: $wh_card->getPricePerUnit()
		);
	}
	
	
	public function in(
		int      $product_id,
		float    $number_of_units,
		Context  $context,
		Currency $currency,
		float    $price_per_unit,
		string   $sector,
		string   $rack,
		string   $position
	) : WarehouseManagement_StockMovement
	{
		return $this->movement(
			type: WarehouseManagement_StockMovement_Type::In(),
			product_id: $product_id,
			number_of_units: $number_of_units,
			context: $context,
			currency: $currency,
			price_per_unit: $price_per_unit,
			sector: $sector,
			rack: $rack,
			position: $position
		);
	}
	
	public function transferIn(
		int      $product_id,
		float    $number_of_units,
		Context  $context,
		Currency $currency,
		float    $price_per_unit,
		string   $sector,
		string   $rack,
		string   $position
	) : WarehouseManagement_StockMovement
	{
		
		return $this->movement(
			type: WarehouseManagement_StockMovement_Type::TransferIn(),
			product_id: $product_id,
			number_of_units: $number_of_units,
			context: $context,
			currency: $currency,
			price_per_unit: $price_per_unit,
			sector: $sector,
			rack: $rack,
			position: $position
		);
	}
	
	
}
