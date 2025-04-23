<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;

use JetApplication\EMail;
use JetApplication\Order;
use JetApplication\Payment_Method;

abstract class Core_Payment_Method_Module extends Application_Module
{
	public function init( Payment_Method $payment_method ) : void
	{
	}
	
	abstract public function handlePayment( Order $order, string $return_url ) : bool;
	
	abstract public function tryAgain( Order $order, string $return_url ) : bool;
	
	abstract public function handlePaymentReturn( Order $order ) : bool;
	
	
	
	public function generateConfirmationEmailInfoText( Order $order, Payment_Method $payment_method ) : string
	{
		return $payment_method->getConfirmationEmailInfoText();
	}
	
	
	public function generateOrderFinalPageInfoText( Order $order, Payment_Method $payment_method ) : string
	{
		return $payment_method->getOrderFinalPageInfoText();
	}
	
	public function updateOrderConfirmationEmail( Order $order, EMail $email ) : void
	{
	
	}
	
	abstract public function getPaymentMethodSpecificationList() : array;
	
	abstract public function getPaymentMethodOptionsList( Payment_Method $payment_method ) : array;

}