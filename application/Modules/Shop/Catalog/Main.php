<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\Catalog;

use Jet\Application_Module;
use JetShop\Shops;

/**
 *
 */
class Main extends Application_Module
{

	/**
	 *
	 * @return string
	 */
	public function getViewsDir(): string
	{
		return Shops::getViewDir().'catalog/';
		//return parent::getViewsDir();
		//return $this->module_manifest->getModuleDir() . static::getDefaultViewsDir() . '/'.Shops::getCurrentId().'/';
	}

}