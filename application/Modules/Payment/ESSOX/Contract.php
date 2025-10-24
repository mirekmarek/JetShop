<?php /** @noinspection PhpStatementHasEmptyBodyInspection */

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\ESSOX;

use Jet\Application_Modules;
use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use JetApplication\EShops;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'essox_contract',
	database_table_name: 'essox_contract',
	id_controller_class: DataModel_IDController_Passive::class
)]
class Contract extends DataModel {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $eshop_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $created_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $customer_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $price = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $down_payment_rounded = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $instalment_number = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $instalment_included_insurance_and_fee = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $contract_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $contract_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $proposal_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $degree = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $firstname = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $surname = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $street = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $house_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $land_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $city = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $email = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $authorization_result_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $authorization_result = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $contract_status_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $contract_status = '';
	
	
	
	public static function newContract( Order $order, $contract_id ) : static
	{
		
		$item = new static();
		
		$item->eshop_key = $order->getEshopKey();
		$item->order_id = $order->getId();
		$item->customer_id = $order->getCustomerId();
		$item->price = round($order->getTotalAmount_WithVAT());
		$item->created_date_time = Data_DateTime::now();
		$item->contract_id = $contract_id;
		
		$item->save();
		$item->actualizeStatus();
		
		return $item;
	}
	
	public function getOrderId() : int
	{
		return $this->order_id;
	}
	
	
	public static function get( int $order_id ) : ?static
	{
		return static::load([
			'order_id' => $order_id,
		]);
	}
	
	
	public function actualizeStatus() : bool
	{
		/**
		 * @var Main $module
		 */
		$module = Application_Modules::moduleInstance('Payment.ESSOX');
		
		$client = new Client( $module->getEshopConfig( EShops::get($this->eshop_key) ) );
		
		$status = $client->getStatus( $this->contract_id );
		
		if(!$status) {
			return false;
		}
		
		$this->contract_number = $status['contractNumber']??'';
		$this->proposal_number = $status['proposalNumber'];
		$this->degree = $status['degree']??'';
		$this->firstname = $status['firstname']??'';
		$this->surname = $status['surname']??'';
		$this->street = $status['street']??'';
		$this->house_number = $status['houseNumber']??'';
		$this->land_number = $status['landNumber']??'';
		$this->city = $status['city']??'';
		$this->zip = $status['zip']??'';
		$this->down_payment_rounded = (float)$status['downPaymentRounded'];
		$this->instalment_number = (float)$status['instalmentNumber'];
		$this->instalment_included_insurance_and_fee = (float)$status['instalmentIncludedInsuranceAndFee'];
		$this->email = $status['email']??'';
		
		$this->contract_status_id = $status['contractStatusId'];
		$this->contract_status = $status['contractStatus'];
		
		$this->authorization_result_id = $status['authorizationResultId']??0;
		$this->authorization_result = $status['authorizationResult']??'';
		
		$this->save();
		
		return true;
	}
	
	public function isAuthorized() : bool
	{
		
		if(in_array($this->authorization_result_id, [2,4,5])) {
			return true;
		}
		
		return false;
	}
	
	
	public static function checkStatuses() : void
	{
		$ids = static::dataFetchCol(
			select: ['order_id'],
			where: [
				'contract_id !=' => '',
				'AND',
				[
					[
						'contract_status_id' => [1,99,12],
						'AND',
						'created_date_time >=' => Data_DateTime::catchDateTime( date('Y-m-d H:m:i', strtotime('-2 months')) )
					],
					'OR',
					[
						'contract_status_id' => [1],
						'AND',
						'created_date_time >=' => Data_DateTime::catchDateTime( date('Y-m-d H:m:i', strtotime('-97 hours')) )
					]
				]
			]
		);
		
		
		
		foreach($ids as $id) {
			echo $id.PHP_EOL;
			
			$contract = static::get($id);
			if($contract) {
				$contract->actualizeStatus();
				
				if($contract->isAuthorized()) {
					/*
					$order = Order::get($contract->getOrderId());
					if($order) {
					}
					*/
				}
			}
		}
	}
	
	public static function checkStatusesAll() : void
	{
		$ids = static::dataFetchCol(
			select: ['order_id'],
			where: [
				'created_date_time >=' => Data_DateTime::catchDateTime( date('Y-m-d H:m:i', strtotime('-2 months')) ),
				'AND',
				'contract_id !=' => ''
			]
		);
		
		
		foreach($ids as $id) {
			echo $id.PHP_EOL;
			
			$contract = static::get($id);
			if($contract) {
				$contract->actualizeStatus();
				
				if($contract->isAuthorized()) {
					/*
					$order = Order::get($contract->getOrderId());
					if($order) {
					}
					*/
				}
			}
		}
	}
	
}