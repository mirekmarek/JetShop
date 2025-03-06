<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Translator;
use JetApplication\PDF;
use JetApplication\PDF_Template_Block;
use JetApplication\PDF_Template_Condition;
use JetApplication\PDF_Template_Property;
use JetApplication\PDF_TemplateProvider;
use JetApplication\PDF_TemplateText;
use JetApplication\PDF_TemplateText_EShopData;
use JetApplication\Managers;
use JetApplication\EShop;

abstract class Core_PDF_Template {
	
	protected string $internal_name = '';
	
	protected string $internal_notes = '';
	
	/**
	 * @var PDF_Template_Property[]
	 */
	protected ?array $properties = null;
	
	/**
	 * @var PDF_Template_Block[]
	 */
	protected ?array $blocks = null;
	/**
	 * @var PDF_Template_Condition[]
	 */
	protected ?array $conditions = null;
	
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
		$this->conditions = [];
		
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
	 * @return PDF_Template_Property[]
	 */
	public function getProperties(): array
	{
		$this->initialize();
		return $this->properties;
	}
	
	/**
	 * @return PDF_Template_Block[]
	 */
	public function getBlocks(): array
	{
		$this->initialize();
		return $this->blocks;
	}
	
	/**
	 * @return PDF_Template_Condition[]
	 */
	public function getConditions(): array
	{
		$this->initialize();
		return $this->conditions;
	}
	
	
	
	public function addProperty( string $name, string $description ) : PDF_Template_Property
	{
		$property = new PDF_Template_Property();
		$property->setName( $name );
		$property->setDescription( $description );
		$this->properties[$property->getName()] = $property;
		
		return $property;
	}
	
	public function addPropertyBlock( string $name, string $description ) : PDF_Template_Block
	{
		$block = new PDF_Template_Block();
		$block->setName( $name );
		$block->setDescription( $description );
		$this->blocks[$block->getName()] = $block;
		
		return $block;
	}
	
	public function addCondition( string $name, string $description ) : PDF_Template_Condition
	{
		$condition = new PDF_Template_Condition();
		$condition->setName( $name );
		$condition->setDescription( $description );
		$this->conditions[$condition->getName()] = $condition;
		
		return $condition;
	}

	
	
	protected function applyProperties( EShop $eshop, PDF $pdf ) : void
	{
		$data = [];
		
		$template_html = $pdf->getTemplateHtml();
		$template_header = $pdf->getTemplateHeader();
		$template_footer = $pdf->getTemplateFooter();
		
		foreach($this->getConditions() as $condition) {
			$condition->processText( $template_html );
			$condition->processText( $template_header );
			$condition->processText( $template_footer );
		}
		
		foreach($this->getProperties() as $property) {
			$property->processText( $template_html );
			$property->processText( $template_header );
			$property->processText( $template_footer );
		}
		
		foreach($this->getBlocks() as $block ) {
			$block->processText( $template_html );
			$block->processText( $template_header );
			$block->processText( $template_footer );
		}
		
		$pdf->setTemplateHtml( $template_html );
		$pdf->setTemplateFooter( $template_footer );
		$pdf->setTemplateHeader( $template_header );
	}
	
	public function preparePDF( EShop $eshop ) : PDF
	{
		/**
		 * @var PDF_TemplateText_EShopData $template
		 */
		$template = PDF_TemplateText_EShopData::getByInternalCode(
			$this->getInternalCode(),
			$eshop
		);
		
		if(!$template) {
			
			$template_master = PDF_TemplateText::getByInternalCode( $this->getInternalCode() );
			
			if(!$template_master) {
				$template_master = new PDF_TemplateText();
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
			
			$template = PDF_TemplateText_EShopData::getByInternalCode(
				$this->getInternalCode(),
				$eshop
			);
			
		}
		
		
		$pdf = new PDF();
		$pdf->setEshop( $eshop );
		$pdf->setTemplate( $template );
		$pdf->setTemplateCode( $template->getInternalCode() );
		$pdf->setTemplateHtml( $template->getTemplateHTML() );
		$pdf->setTemplateFooter( $template->getTemplateFooter() );
		$pdf->setTemplateHeader( $template->getTemplateHeader() );
		
		$this->setupPDF( $eshop, $pdf );
		
		$this->applyProperties( $eshop, $pdf );
		
		return $pdf;
		
	}

	public function generatePDF( EShop $eshop ) : string
	{
		return $this->preparePDF( $eshop )->generatePDF();
	}
	
	abstract public function setupPDF( EShop $eshop, PDF $pdf ) : void;
	
	
	/**
	 * @return static[]
	 */
	public static function findAllTemplates() : array
	{
		if(static::$all_templates===null) {
			static::$all_templates = [];
			
			foreach( Managers::findManagers(PDF_TemplateProvider::class) as $module) {
				/**
				 * @var PDF_TemplateProvider|Application_Module $module
				 */
				
				Translator::setCurrentDictionaryTemporary(
					dictionary: $module->getModuleManifest()->getName(),
					action: function() use ($module) {
						$module_templates = $module->getPDFTemplates();
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