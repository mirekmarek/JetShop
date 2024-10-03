<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Shop_OAuth_BackendModule;

abstract class Core_Shop_Managers_OAuth extends Application_Module
{
	
	/**
	 * @return Shop_OAuth_BackendModule[]
	 */
	abstract public function getOAuthModules(): array;
	
	abstract public function handle( Shop_OAuth_BackendModule $module ): void;
	
	abstract public function renderLoginButton( Shop_OAuth_BackendModule $module ) : string;
	
}