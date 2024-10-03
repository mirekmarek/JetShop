<?php
namespace JetShop;


use Jet\MVC_View;
use JetApplication\Shop_Template;

interface Core_Shop_ModuleUsingTemplate_Interface
{
	public function getModuleViewsDir() : string;
	
	public function getTemplateViewsDir( Shop_Template $template ) : string;
	
	public function copyModuleDefaultViewsToTemplateDir( Shop_Template $template ) : void;
	
	public function getViewsDir(): string;
	
	public function getView() : MVC_View;
}