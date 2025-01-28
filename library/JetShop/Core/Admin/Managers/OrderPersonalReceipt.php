<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Context;
use JetApplication\OrderPersonalReceipt;

interface Core_Admin_Managers_OrderPersonalReceipt extends Admin_EntityManager_Interface
{
	public function showDispatches( Context $context ) : string;
	
	public function showOrderPersonalReceiptStatus( OrderPersonalReceipt $dispatch ) : string;
	
}