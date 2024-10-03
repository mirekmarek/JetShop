<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EMail_Template;
use JetApplication\EMail_TemplateText_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops_Shop;



#[DataModel_Definition(
	name: 'email_templates',
	database_table_name: 'email_templates',
)]
abstract class Core_EMail_TemplateText extends Entity_WithShopData
{
	
	
	/**
	 * @var EMail_TemplateText_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: EMail_TemplateText_ShopData::class
	)]
	protected array $shop_data = [];
	
	
	
	public function getShopData( ?Shops_Shop $shop = null ): EMail_TemplateText_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
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