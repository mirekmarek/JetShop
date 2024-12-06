<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\EShop\Catalog;

use Jet\Application_Module;
use JetApplication\EShop_Managers_Catalog;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;

/**
 *
 */
class Main extends Application_Module implements EShop_Managers_Catalog, EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
}