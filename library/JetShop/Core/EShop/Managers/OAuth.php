<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\EShop_OAuth_BackendModule;

abstract class Core_EShop_Managers_OAuth extends Application_Module
{
	
	/**
	 * @return EShop_OAuth_BackendModule[]
	 */
	abstract public function getOAuthModules(): array;
	
	abstract public function handle( EShop_OAuth_BackendModule $module ): void;
	
	abstract public function renderLoginButton( EShop_OAuth_BackendModule $module ) : string;
	
}