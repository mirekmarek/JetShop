<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Payment\GoPay;


abstract class GoPay_Logger {

	abstract public function start( string $message ) : void;
	abstract public function step( string $message ) : void;
	abstract public function doneError( string $error_code, mixed $API_response, array $payment_data=[] ) : void;
	abstract public function doneSuccess() : void;
}