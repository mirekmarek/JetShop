<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Service_List;
use Jet\Translator;
use JetApplication\SMS;
use JetApplication\SMS_TemplateProvider;
use JetApplication\SMS_TemplateText;
use JetApplication\SMS_TemplateText_EShopData;
use JetApplication\EShop;
use JetApplication\Template;

abstract class Core_SMS_Template extends Template
{
	
	protected static ?array $all_templates = null;
	
	

	public function createSMS( EShop $eshop, bool $template_must_be_active = true ) : ?SMS
	{
		
		/**
		 * @var SMS_TemplateText_EShopData $template
		 */
		$template = SMS_TemplateText_EShopData::getByInternalCode(
			$this->getInternalCode(),
			$eshop
		);
		
		if(!$template) {
			
			$template_master = SMS_TemplateText::getByInternalCode( $this->getInternalCode() );
			
			if(!$template_master) {
				$template_master = new SMS_TemplateText();
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
			
			$template = SMS_TemplateText_EShopData::getByInternalCode(
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
		
		
		$text = $template->getText();
		
		
		$sms = new SMS();
		$sms->setEshop( $eshop );
		
		$sms->setText( $this->process( $text ) );
		
		$this->setupSMS( $eshop, $sms );
		
		return $sms;
	}
	
	abstract public function setupSMS( EShop $eshop, SMS $sms ) : void;
	
	
	public function createTestSMS( EShop $eshop ) : SMS
	{
		$this->initTest( $eshop );
		$sms = $this->createSMS( $eshop, false );
		$sms->setSaveHistoryAfterSend( false );
		
		return $sms;
	}
	
	
	
	/**
	 * @return static[]
	 */
	public static function findAllTemplates() : array
	{
		if(static::$all_templates===null) {
			static::$all_templates = [];
			
			foreach( Application_Service_List::findPossibleModules(SMS_TemplateProvider::class) as $module) {
				/**
				 * @var SMS_TemplateProvider|Application_Module $module
				 */
				Translator::setCurrentDictionaryTemporary(
					dictionary: $module->getModuleManifest()->getName(),
					action: function() use ($module) {
						$module_templates = $module->getSMSTemplates();
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