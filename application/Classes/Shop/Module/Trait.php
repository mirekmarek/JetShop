<?php
namespace JetShop;

trait Shop_Module_Trait {

	protected function _getViewsDir( string $part ): string
	{
		return Shops::getViewDir().$part.'/';
		//return parent::getViewsDir();
		//return $this->module_manifest->getModuleDir() . static::getDefaultViewsDir() . '/'.Shops::getCurrentId().'/';
	}

}