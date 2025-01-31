<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Tr;
use Jet\Translator;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\EShop;

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
	
	public function handleControlCentre( ?EShop $eshop=null ) : string
	{
		/**
		 * @var Application_Module|Admin_ControlCentre_Module_Interface $this
		 */
		
		return Translator::setCurrentDictionaryTemporary( $this->getModuleManifest()->getName(), function() use ($eshop) : string {
			$page_content = Factory_MVC::getPageContentInstance();
			
			$page_content->setModuleName( $this->getModuleManifest()->getName() );
			$page_content->setControllerName( 'ControlCentre' );
			$page_content->setParameter( 'eshop', $eshop );
			
			$page_content->dispatch();
			
			return $page_content->getOutput();
		});
	}

	
}