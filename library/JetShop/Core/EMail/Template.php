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
use JetApplication\EMail_TemplateProvider;
use JetApplication\EMail_TemplateText;
use JetApplication\EMail_TemplateText_EShopData;
use JetApplication\Managers;
use JetApplication\EShop;
use JetApplication\Template;

abstract class Core_EMail_Template extends Template
{
	
	protected static ?array $all_templates = null;
	
	

	public function createEmail( EShop $eshop, bool $template_must_be_active = true ) : ?EMail
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
				$template_master->checkEShopData();
				$template_master->setInternalCode( $this->getInternalCode() );
				$template_master->setInternalName( $this->getInternalName() );
				$template_master->setInternalNotes( $this->getInternalNotes() );
				
				$template_master->save();
			} else {
				$template_master->checkEShopData();
				$template_master->setInternalCode( $this->getInternalCode() );
				$template_master->save();
			}
			
			$template = EMail_TemplateText_EShopData::getByInternalCode(
				$this->getInternalCode(),
				$eshop
			);
			
		}
		
		if(
			$template_must_be_active &&
			!$template->isActive()
		) {
			return null;
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
		
		$email->setSubject( $this->process( $template->getSubject() ) );
		$email->setBodyTxt( $this->process( $body_txt ) );
		$email->setBodyHtml( $this->process( $body_html ) );
		
		foreach($template->getAttachments() as $attachment) {
			$email->addAttachments( $attachment->getPath() );
		}
		
		$this->setupEMail( $eshop, $email );
		
		return $email;
	}
	
	abstract public function setupEMail( EShop $eshop, EMail $email ) : void;
	
	
	public function createTestEmail( EShop $eshop ) : EMail
	{
		$this->initTest( $eshop );
		$email = $this->createEmail( $eshop, false );
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