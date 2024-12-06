<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EMail_Template;
use JetApplication\EMail_TemplateText_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;



#[DataModel_Definition(
	name: 'email_templates',
	database_table_name: 'email_templates',
)]
abstract class Core_EMail_TemplateText extends Entity_WithEShopData
{
	
	
	/**
	 * @var EMail_TemplateText_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: EMail_TemplateText_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	
	public function getEshopData( ?EShop $eshop = null ): EMail_TemplateText_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	/**
	 * @return EMail_Template[]
	 */
	public static function actualizeList() : array
	{
		$templates = EMail_Template::findAllTemplates();

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
}