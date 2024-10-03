<?php

/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Payment\GoPay;

use Jet\Logger as JetLogger;

class Logger extends GoPay_Logger
{
	protected string $session = '';
	
	public function start( string $message ): void
	{
		$this->session = $message;
	}
	
	public function step( string $message ): void
	{
		$this->session .= "\n".$message;
	}
	
	public function doneError( string $error_code, mixed $API_response, array $payment_data = [] ): void
	{
		JetLogger::danger(
			event: 'payment_GoPay_error',
			event_message: 'GoPay payment error',
			context_object_data: [
				'API_response' => $API_response,
				'payment_data' => $payment_data
			]
		);
	}
	
	public function doneSuccess(): void
	{
	}
}