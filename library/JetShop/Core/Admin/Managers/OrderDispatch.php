<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Context;
use JetApplication\OrderDispatch;

interface Core_Admin_Managers_OrderDispatch extends Admin_EntityManager_Interface
{
	public function showDispatches( Context $context ) : string;
	
	public function showOrderDispatchStatus( OrderDispatch $dispatch ) : string;
	
	public function showRecipient( OrderDispatch $dispatch ) : string;
	
	
}