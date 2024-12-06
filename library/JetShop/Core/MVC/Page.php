<?php
namespace JetShop;

use Jet\MVC;
use Jet\MVC_Page;
use Jet\SysConf_Jet_MVC;
use JetApplication\EShop;
use JetApplication\EShop_PageDefinition;

abstract class Core_MVC_Page extends MVC_Page
{
	protected string $definition_key = '';
	
	public function getDefinitionKey(): string
	{
		return $this->definition_key;
	}
	
	public function setDefinitionKey( string $definition_key ): void
	{
		$this->definition_key = $definition_key;
	}
	
	
	
	public function initByDefinition( EShop $eshop, EShop_PageDefinition $definition ) : void
	{
		$locale = $eshop->getLocale();
		
		$this->setDefinitionKey( $definition->getKey() );
		
		$this->setId( $definition->getId() );
		
		$this->setBase( MVC::getBase( $eshop->getBaseId() ) );
		$this->setLocale( $eshop->getLocale() );
		$this->setName( $definition->getName() );
		
		
		$parent_definition = $definition->getParentDefinition();
		
		if($parent_definition) {
			
			$this->setParentId( $parent_definition->getId() );
			$this->relative_path_fragment = $definition->getURIPathFragment();
			$this->relative_path = $definition->getURIPath();
		}
		
		$this->setLayoutScriptName( $definition->getLayoutScriptName() );
		
		$this->setTitle( $definition->getTitle( $locale ) );
		$this->setBreadcrumbTitle( $definition->getTitle( $locale ) );
		$this->setMenuTitle( $definition->getTitle( $locale ) );
		
		$this->setIsActive( true );
		$this->setIsSecret( $definition->getIsSecrtet() );
		$this->setIcon( $definition->getIcon() );
		
		foreach($definition->getContent() as $content) {
			$this->addContent( $content->createPageContentDefinition( $eshop ) );
		}
		
		$this->_data_file_path =
			rtrim( MVC::getBase($eshop->getBaseId())->getPagesDataPath($eshop->getLocale())
			.$this->getRelativePath(), '/').'/'
			.SysConf_Jet_MVC::getPageDataFileName();
		
		
	}
	
}