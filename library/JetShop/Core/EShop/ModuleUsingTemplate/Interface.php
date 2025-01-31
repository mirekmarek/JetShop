<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\MVC_View;
use JetApplication\EShop_Template;

interface Core_EShop_ModuleUsingTemplate_Interface
{
	public function getModuleViewsDir() : string;
	
	public function getTemplateViewsDir( EShop_Template $template ) : string;
	
	public function copyModuleDefaultViewsToTemplateDir( EShop_Template $template ) : void;
	
	public function getViewsDir(): string;
	
	public function getView() : MVC_View;
}