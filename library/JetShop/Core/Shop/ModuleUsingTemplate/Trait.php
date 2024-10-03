<?php
namespace JetShop;


use Jet\Factory_MVC;
use Jet\IO_Dir;
use Jet\MVC;
use Jet\MVC_View;
use Jet\SysConf_Jet_Modules;
use JetApplication\Application_Admin;
use JetApplication\Shop_Template;
use JetApplication\Shops;

trait Core_Shop_ModuleUsingTemplate_Trait
{
	
	public function getModuleViewsDir() : string
	{
		return $this->module_manifest->getModuleDir() . SysConf_Jet_Modules::getViewsDir() . '/';
	}
	
	public function getTemplateViewsDir( Shop_Template $template ) : string
	{
		return $template->getViewsDir().$this->module_manifest->getName() . '/';
	}
	
	public function copyModuleDefaultViewsToTemplateDir( Shop_Template $template ) : void
	{
		IO_Dir::copy(
			source_path: $this->getModuleViewsDir(),
			target_path: $this->getTemplateViewsDir( $template ),
			overwrite_if_exists: true
		);
	}
	
	
	public function getViewsDir(): string
	{
		if(MVC::getBase()->getId()==Application_Admin::getBaseId()) {
			return $this->getModuleViewsDir();
		}
		
		$shop = Shops::getCurrent();
		if(!$shop->getUseTemplate()) {
			return $this->getModuleViewsDir();
		}
		
		$template = $shop->getTemplate();
		
		
		$template_view_dir = $this->getTemplateViewsDir( $template );
		if(!IO_Dir::exists($template_view_dir)) {
			$this->copyModuleDefaultViewsToTemplateDir( $template );
		}
		
		
		return $template_view_dir;
	}
	
	public function getView() : MVC_View
	{
		return Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
}