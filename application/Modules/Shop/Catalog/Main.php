<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Catalog;

use Jet\Application_Module;
use JetApplication\Shop_Managers_Catalog;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_Catalog, Shop_ModuleUsingTemplate_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
}