<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\SMS_TemplateText;


#[DataModel_Definition(
	name: 'sms_templates_eshop_data',
	database_table_name: 'sms_templates_eshop_data',
	parent_model_class: SMS_TemplateText::class
)]
abstract class Core_SMS_TemplateText_EShopData extends EShopEntity_WithEShopData_EShopData
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Text:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $text = '';
	
	public function getText(): string
	{
		return $this->text;
	}
	
	public function setText( string $text ): void
	{
		$this->text = $text;
	}
}