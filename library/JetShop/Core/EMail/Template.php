<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Translator;
use JetApplication\EMail;
use JetApplication\EMail_Layout_EShopData;
use JetApplication\EMail_Template_Block;
use JetApplication\EMail_Template_Property;
use JetApplication\EMail_TemplateProvider;
use JetApplication\EMail_TemplateText;
use JetApplication\EMail_TemplateText_EShopData;
use JetApplication\Managers;
use JetApplication\EShop;

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
	
	public function initTest( EShop $eshop ) : void
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
	

	
	
	protected function applyProperties( EShop $eshop, EMail $email ) : void
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
	

	public function createEmail( EShop $eshop ) : EMail
	{
		
		/**
		 * @var EMail_TemplateText_EShopData $template
		 */
		$template = EMail_TemplateText_EShopData::getByInternalCode(
			$this->getInternalCode(),
			$eshop
		);
		
		if(!$template) {
			
			$template_master = EMail_TemplateText::getByInternalCode( $this->getInternalCode() );
			
			if(!$template_master) {
				$template_master = new EMail_TemplateText();
				$template_master->checkShopData();
				$template_master->setInternalCode( $this->getInternalCode() );
				$template_master->setInternalName( $this->getInternalName() );
				$template_master->setInternalNotes( $this->getInternalNotes() );
				
				$template_master->save();
				
				$template_master->activateCompletely();
			} else {
				$template_master->checkShopData();
				$template_master->setInternalCode( $this->getInternalCode() );
				$template_master->save();
				$template_master->activateCompletely();
			}
			
			$template = EMail_TemplateText_EShopData::getByInternalCode(
				$this->getInternalCode(),
				$eshop
			);
			
		}
		
		$placeholder = '%body%';
		
		
		$body_html = $template->getBodyHTML();
		
		$body_txt = $template->getBodyTxt();
		
		if($template->getLayoutId()) {
			$layout = EMail_Layout_EShopData::get( $template->getLayoutId(), $eshop );
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
		$email->setEshop( $eshop );
		$email->setTemplateCode( $template->getInternalCode() );
		$email->setSenderEmail( $template->getSenderEmail() );
		$email->setSenderName( $template->getSenderName() );
		$email->setSubject( $template->getSubject() );
		$email->setBodyTxt( $body_txt );
		$email->setBodyHtml( $body_html );
		
		$this->setupEMail( $eshop, $email );
		
		$this->applyProperties( $eshop, $email );
		
		return $email;
	}
	
	abstract public function setupEMail( EShop $eshop, EMail $email ) : void;
	
	
	public function createTestEmail( EShop $eshop ) : EMail
	{
		$this->initTest( $eshop );
		$email = $this->createEmail( $eshop );
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