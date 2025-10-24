<?php /** @noinspection PhpStatementHasEmptyBodyInspection */

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\HomeCredit;

use Jet\Application_Modules;
use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'home_credit_contract',
	database_table_name: 'home_credit_contract',
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
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $application_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $state = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $state_reason = '';
	
	
	
	public static function newContract(  EShop $eshop, int $order_id, string $application_id ) : static
	{
		$exists = self::load([
			'eshop_key' => $eshop->getKey(),
			'AND',
			'order_id' => $order_id,
		]);
		
		if($exists) {
			$exists->setApplicationId($application_id);
			$exists->save();
			$exists->actualizeStatus();
			return $exists;
		}
		
		$item = new static();
		
		$item->eshop_key = $eshop->getKey();
		$item->created_date_time = Data_DateTime::now();
		
		$item->order_id = $order_id;
		$item->application_id = $application_id;
		
		$item->save();
		$item->actualizeStatus();
		
		return $item;
	}
	
	public function getOrderId() : int
	{
		return $this->order_id;
	}
	
	public function getApplicationId(): string
	{
		return $this->application_id;
	}
	
	public function setApplicationId( string $application_id ): void
	{
		$this->application_id = $application_id;
	}
	
	public function getState(): string
	{
		return $this->state;
	}
	
	public function setState( string $state ): void
	{
		$this->state = $state;
	}
	
	public function getStateReason(): string
	{
		return $this->state_reason;
	}
	
	public function setStateReason( string $state_reason ): void
	{
		$this->state_reason = $state_reason;
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
		$module = Application_Modules::moduleInstance('Payment.HomeCredit');
		
		/** @noinspection PhpParamsInspection */
		$client = new Client( $module->getEshopConfig( EShops::get($this->eshop_key) ) );
		
		
		$credit_application = $client->getCreditApplicationDetail( $this->getApplicationId() );
		if(!$credit_application) {
			return false;
		}
		
		
		
		$this->setState( $credit_application['state'] );
		$this->setStateReason( $credit_application['stateReason'] );
		$this->save();

		$this->save();
		
		return true;
	}
	
	public function isAuthorized() : bool
	{
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