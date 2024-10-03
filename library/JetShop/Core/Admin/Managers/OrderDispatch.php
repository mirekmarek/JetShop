<?php
namespace JetShop;

use JetApplication\Context;
use JetApplication\OrderDispatch;

interface Core_Admin_Managers_OrderDispatch
{
	public function showName( int $id ) : string;
	
	public function showDispatches( Context $context ) : string;
	
	public function showOrderDispatchStatus( OrderDispatch $dispatch ) : string;
	
	public function showRecipient( OrderDispatch $dispatch ) : string;
	
	public function getOrderDispatchURL( int $id ) : string;
	
}