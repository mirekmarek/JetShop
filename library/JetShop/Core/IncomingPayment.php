<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Context_HasContext_Interface;
use JetApplication\Context_HasContext_Trait;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\EShop;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasOrderContext_Interface;
use JetApplication\EShopEntity_HasOrderContext_Trait;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'incoming_payments',
	database_table_name: 'incoming_payments',
)]
abstract class Core_IncomingPayment extends EShopEntity_WithEShopRelation implements
	EShopEntity_HasGet_Interface,
	EShopEntity_HasOrderContext_Interface,
	Context_HasContext_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasOrderContext_Trait;
	use Context_HasContext_Trait;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $source = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $payment_reference_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_and_time_of_detection = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $amount = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 10,
		is_key: true,
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	protected string $vs = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $from_account_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $from_account_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $from_bank_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $paired = false;
	
	public function getSource(): string
	{
		return $this->source;
	}
	
	public function setSource( string $source ): void
	{
		$this->source = $source;
	}
	
	public function getPaymentReferenceNumber(): string
	{
		return $this->payment_reference_number;
	}
	
	public function setPaymentReferenceNumber( string $payment_reference_number ): void
	{
		$this->payment_reference_number = $payment_reference_number;
	}
	
	public function getDateAndTimeOfDetection(): ?Data_DateTime
	{
		return $this->date_and_time_of_detection;
	}
	
	public function setDateAndTimeOfDetection( ?Data_DateTime $date_and_time_of_detection ): void
	{
		$this->date_and_time_of_detection = $date_and_time_of_detection;
	}
	
	public function getAmount(): float
	{
		return $this->amount;
	}
	
	public function setAmount( float $amount ): void
	{
		$this->amount = $amount;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function getCurrency(): Currency
	{
		return Currencies::get( $this->currency_code );
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	public function getVs(): string
	{
		return $this->vs;
	}
	
	public function setVs( string $vs ): void
	{
		$this->vs = $vs;
	}
	
	public function getFromAccountNumber(): string
	{
		return $this->from_account_number;
	}
	
	public function setFromAccountNumber( string $from_account_number ): void
	{
		$this->from_account_number = $from_account_number;
	}
	
	public function getFromAccountName(): string
	{
		return $this->from_account_name;
	}
	
	public function setFromAccountName( string $from_account_name ): void
	{
		$this->from_account_name = $from_account_name;
	}
	
	public function getFromBankCode(): string
	{
		return $this->from_bank_code;
	}
	
	public function setFromBankCode( string $from_bank_code ): void
	{
		$this->from_bank_code = $from_bank_code;
	}
	
	public function isPaired(): bool
	{
		return $this->paired;
	}
	
	public function setPaired( bool $paired ): void
	{
		$this->paired = $paired;
	}
	
	public static function add(
		EShop $eshop,
		string $source,
		string $payment_reference_number,
		Data_DateTime $date_and_time_of_detection,
		float $amount,
		Currency $currency,
		string $vs,
		string $from_account_number,
		string $from_account_name,
		string $from_bank_code
	) : static
	{
		$exists = static::load([
			$eshop->getWhere(),
			'AND',
			'source' => $source,
			'AND',
			'payment_reference_number' => $payment_reference_number,
			'AND',
			'currency_code' => $currency->getCode(),
			'AND',
			'vs' => $vs,
		]);
		if($exists) {
			return $exists;
		}
		
		$new = new static();
		$new->setEshop( $eshop );
		$new->setSource( $source );
		$new->setPaymentReferenceNumber( $payment_reference_number );
		$new->setDateAndTimeOfDetection( $date_and_time_of_detection );
		$new->setAmount( $amount );
		$new->setCurrencyCode( $currency->getCode() );
		$new->setVs( $vs );
		$new->setFromAccountNumber( $from_account_number );
		$new->setFromAccountName( $from_account_name );
		$new->setFromBankCode( $from_bank_code );
		$new->save();
		
		return $new;
	}
	
	/**
	 * @return array<static>
	 */
	public static function getOrderPayments( Order $order ) : array
	{
		$relevant_payments = static::fetch([''=>[
			$order->getEshop()->getWhere(),
			'AND',
			'currency_code' => $order->getCurrency()->getCode(),
			'AND',
			'vs' => $order->getNumber()
		]]);
		

		return $relevant_payments;
	}
	
}