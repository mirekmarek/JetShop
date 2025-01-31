<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\GoPay;


class GoPay_CreatedPayment
{
	protected string $URL;
	protected string $payment_id = '';
	
	public function getURL(): string
	{
		return $this->URL;
	}
	
	public function setURL( string $URL ): void
	{
		$this->URL = $URL;
	}
	
	public function getPaymentId(): string
	{
		return $this->payment_id;
	}
	
	public function setPaymentId( string $payment_id ): void
	{
		$this->payment_id = $payment_id;
	}
	
	
}