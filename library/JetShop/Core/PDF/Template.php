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
use JetApplication\PDF_TemplateProvider;
use JetApplication\PDF_TemplateText;
use JetApplication\PDF_TemplateText_EShopData;
use JetApplication\Managers;
use JetApplication\EShop;
use JetApplication\Template;

abstract class Core_PDF_Template extends Template {
	
	protected static ?array $all_templates = null;
	
	
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
				$template_master->checkEShopData();
				$template_master->setInternalCode( $this->getInternalCode() );
				$template_master->setInternalName( $this->getInternalName() );
				$template_master->setInternalNotes( $this->getInternalNotes() );
				
				$template_master->save();
				
				$template_master->activateCompletely();
			} else {
				$template_master->checkEShopData();
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
		
		$pdf->setTemplateHtml( $this->process( $template->getTemplateHTML() ) );
		$pdf->setTemplateFooter( $this->process( $template->getTemplateFooter() ) );
		$pdf->setTemplateHeader( $this->process( $template->getTemplateHeader() ) );
		
		$this->setupPDF( $eshop, $pdf );
		
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