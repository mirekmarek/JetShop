<?php
namespace JetShop;


use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Tr;
use Jet\Translator;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Shops_Shop;

trait Core_Admin_ControlCentre_Module_Trait {
	
	public function getControlCentreTitleTranslated() : string
	{
		/**
		 * @var Application_Module|Admin_ControlCentre_Module_Interface $this
		 */
		
		return Translator::setCurrentDictionaryTemporary( $this->getModuleManifest()->getName(), function() : string {
			return Tr::_( $this->getControlCentreTitle() );
		} );
	}
	
	public function handleControlCentre( ?Shops_Shop $shop=null ) : string
	{
		/**
		 * @var Application_Module|Admin_ControlCentre_Module_Interface $this
		 */
		
		return Translator::setCurrentDictionaryTemporary( $this->getModuleManifest()->getName(), function() use ($shop) : string {
			$page_content = Factory_MVC::getPageContentInstance();
			
			$page_content->setModuleName( $this->getModuleManifest()->getName() );
			$page_content->setControllerName( 'ControlCentre' );
			$page_content->setParameter( 'shop', $shop );
			
			$page_content->dispatch();
			
			return $page_content->getOutput();
		});
	}

	
}