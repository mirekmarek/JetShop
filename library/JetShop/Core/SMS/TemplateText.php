<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form;
use JetApplication\Application_Service_Admin_Content_SMSTemplates;
use JetApplication\SMS_Template;
use JetApplication\SMS_TemplateText_EShopData;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShop;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'sms_templates',
	database_table_name: 'sms_templates',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'SMS template',
	admin_manager_interface: Application_Service_Admin_Content_SMSTemplates::class,
	separate_tab_form_shop_data: true
)]
abstract class Core_SMS_TemplateText extends EShopEntity_WithEShopData implements EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_Admin_WithEShopData_Trait;
	
	/**
	 * @var SMS_TemplateText_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: SMS_TemplateText_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	
	public function getEshopData( ?EShop $eshop = null ): SMS_TemplateText_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	/**
	 * @return SMS_Template[]
	 */
	public static function actualizeList() : array
	{
		$templates = SMS_Template::findAllTemplates();

		foreach($templates as $template) {
			if(
				!$template->getInternalCode() ||
				!$template->getInternalName()
			) {
				continue;
			}
			
			$template_text = static::load([
				'internal_code' => $template->getInternalCode()
			]);
			
			if(!$template_text) {
				$template_text = new static();
				$template_text->setInternalCode( $template->getInternalCode() );
				$template_text->setInternalName( $template->getInternalName() );
				$template_text->setInternalNotes( $template->getInternalNotes() );
				
				$template_text->save();
				
				$template_text->activateCompletely();
			}
		}
		
		return $templates;
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$form->field('internal_code')->setIsReadonly( true );
	}
	
	protected function setupAddForm( Form $form ): void
	{
	}
	
}