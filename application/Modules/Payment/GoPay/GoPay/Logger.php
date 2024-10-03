<?php
namespace JetApplicationModule\Payment\GoPay;

abstract class GoPay_Logger {

	abstract public function start( string $message ) : void;
	abstract public function step( string $message ) : void;
	abstract public function doneError( string $error_code, mixed $API_response, array $payment_data=[] ) : void;
	abstract public function doneSuccess() : void;
}