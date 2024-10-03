<?php
/**
 *
 */

namespace JetShop;

use Jet\Translator;
use JetApplication\EMail;
use JetApplication\EMail_Layout_ShopData;
use JetApplication\EMail_Template_Block;
use JetApplication\EMail_Template_Property;
use JetApplication\EMail_TemplateProvider;
use JetApplication\EMail_TemplateText_ShopData;
use JetApplication\Managers;
use JetApplication\Shops_Shop;

abstract class Core_EMail_Template {
	
	protected string $internal_name = '';
	
	protected string $internal_notes = '';
	
	/**
	 * @var EMail_Template_Property[]
	 */
	protected ?array $properties = null;
	
	/**
	 * @var EMail_Template_Block[]
	 */
	protected ?array $blocks = null;
	
	protected bool $initialized = false;
	
	protected static ?array $all_templates = null;

	
	public function __construct()
	{
	}
	
	protected function initialize() : void
	{
		if($this->properties!==null) {
			return;
		}
		
		$this->properties = [];
		$this->blocks = [];
		
		$this->init();
		
	}
	
	public function initTest( Shops_Shop $shop ) : void
	{
	
	}
	
	abstract protected function init() : void;
	
	public function getInternalName(): string
	{
		$this->initialize();
		return $this->internal_name;
	}
	
	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}

	public function getInternalCode(): string
	{
		return get_class($this);
	}

	public function setInternalCode( string $internal_code ): void
	{
	}
	
	public function getInternalNotes(): string
	{
		$this->initialize();
		return $this->internal_notes;
	}
	
	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
	}
	
	/**
	 * @return EMail_Template_Property[]
	 */
	public function getProperties(): array
	{
		$this->initialize();
		return $this->properties;
	}
	
	/**
	 * @return EMail_Template_Block[]
	 */
	public function getBlocks(): array
	{
		$this->initialize();
		return $this->blocks;
	}
	
	
	
	public function addProperty( string $name, string $description ) : EMail_Template_Property
	{
		$property = new EMail_Template_Property();
		$property->setName( $name );
		$property->setDescription( $description );
		$this->properties[$property->getName()] = $property;
		
		return $property;
	}
	
	public function addPropertyBlock( string $name, string $description ) : EMail_Template_Block
	{
		$block = new EMail_Template_Block();
		$block->setName( $name );
		$block->setDescription( $description );
		$this->blocks[$block->getName()] = $block;
		
		return $block;
	}
	

	
	
	protected function applyProperties( Shops_Shop $shop, EMail $email ) : void
	{
		$data = [];
		
		$subject = $email->getSubject();
		$body_txt = $email->getBodyTxt();
		$body_html = $email->getBodyHtml();
		
		foreach($this->getProperties() as $property) {
			$property->processText( $subject );
			$property->processText( $body_txt );
			$property->processText( $body_html );
		}
		
		foreach($this->getBlocks() as $block ) {
			$block->processText( $subject );
			$block->processText( $body_txt );
			$block->processText( $body_html );
		}
		
		$email->setSubject( $subject );
		$email->setBodyTxt( $body_txt );
		$email->setBodyHtml( $body_html );
	}
	

	public function createEmail( Shops_Shop $shop ) : EMail
	{
		
		/**
		 * @var EMail_TemplateText_ShopData $template
		 */
		$template = EMail_TemplateText_ShopData::getByInternalCode(
			$this->getInternalCode(),
			$shop
		);
		
		$placeholder = '%body%';
		
		
		$body_html = $template->getBodyHTML();
		
		$body_txt = $template->getBodyTxt();
		
		if($template->getLayoutId()) {
			$layout = EMail_Layout_ShopData::get( $template->getLayoutId(), $shop );
			if($layout) {
				
				if(str_contains($layout->getLayoutHTML(), $placeholder)) {
					$body_html = str_replace($placeholder, $body_html, $layout->getLayoutHTML());
				}
				
				if(str_contains($layout->getLayoutTxt(), $placeholder)) {
					$body_txt = str_replace($placeholder, $body_txt, $layout->getLayoutTxt());
				}
				
			}
		}
		
		
		$email = new EMail();
		$email->setShop( $shop );
		$email->setTemplateCode( $template->getInternalCode() );
		$email->setSenderEmail( $template->getSenderEmail() );
		$email->setSenderName( $template->getSenderName() );
		$email->setSubject( $template->getSubject() );
		$email->setBodyTxt( $body_txt );
		$email->setBodyHtml( $body_html );
		
		$this->setupEMail( $shop, $email );
		
		$this->applyProperties( $shop, $email );
		
		return $email;
	}
	
	abstract public function setupEMail( Shops_Shop $shop, EMail $email ) : void;
	
	
	public function createTestEmail( Shops_Shop $shop ) : EMail
	{
		$this->initTest( $shop );
		$email = $this->createEmail( $shop );
		$email->setSaveHistoryAfterSend( false );
		
		return $email;
	}
	
	
	
	/**
	 * @return static[]
	 */
	public static function findAllTemplates() : array
	{
		if(static::$all_templates===null) {
			static::$all_templates = [];
			
			foreach( Managers::findManagers(EMail_TemplateProvider::class) as $module) {
				
					Translator::setCurrentDictionaryTemporary(
						dictionary: $module->getModuleManifest()->getName(),
						action: function() use ($module) {
							$module_templates = $module->getEMailTemplates();
							foreach($module_templates as $template) {
								$template->initialize();
								static::$all_templates[$template->getInternalCode()] = $template;
							}
						}
					);
					
				
			}
			
		}
		
		return static::$all_templates;
	}
}