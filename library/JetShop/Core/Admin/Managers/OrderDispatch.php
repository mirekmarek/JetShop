<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\Context;
use JetApplication\OrderDispatch;

abstract class Core_Admin_Managers_OrderDispatch extends Admin_EntityManager_Module
{
	abstract public function showDispatches( Context $context ) : string;
	
	abstract public function showOrderDispatchStatus( OrderDispatch $dispatch ) : string;
	
	abstract public function showRecipient( OrderDispatch $dispatch ) : string;
	
}