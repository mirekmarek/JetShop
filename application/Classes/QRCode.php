<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\Data_DateTime;

abstract class QRCode {
	
	protected ?Data_DateTime $date_time = null;
	protected ?Currency $currency;
	protected string $account_number = '';
	protected string $bank_code = '';
	protected string $message = '';
	protected float $amount = 0.0;
	protected string $vs = '';
	protected string $ss = '';
	protected string $ks = '';
	
	
	abstract public function getURL() : string;
	
	abstract public function getFilePath() : string;
	
	public function getDateTime(): ?Data_DateTime
	{
		return $this->date_time;
	}
	
	
	public function getAccountNumber(): string
	{
		return $this->account_number;
	}
	
	public function getBankCode(): string
	{
		return $this->bank_code;
	}
	
	public function getBankAccountNumber(): string
	{
		return $this->account_number.' / '.$this->bank_code;
	}
	
	public function getCurrency() : Currency
	{
		return $this->currency;
	}
	
	
	public function getAmount(): float
	{
		return $this->amount;
	}
	
	public function getVs(): string
	{
		return $this->vs;
	}
	
	public function getSs(): string
	{
		return $this->ss;
	}
	
	public function getKs(): string
	{
		return $this->ks;
	}
	
	
}