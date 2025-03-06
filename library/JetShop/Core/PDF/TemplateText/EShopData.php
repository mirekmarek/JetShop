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
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\PDF_TemplateText;


#[DataModel_Definition(
	name: 'pdf_templates_eshop_data',
	database_table_name: 'pdf_templates_eshop_data',
	parent_model_class: PDF_TemplateText::class
)]
abstract class Core_PDF_TemplateText_EShopData extends EShopEntity_WithEShopData_EShopData
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Template - HTML:'
	)]
	protected string $template_html = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Footer:'
	)]
	protected string $template_header = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Footer:'
	)]
	protected string $template_footer = '';
	
	public function setTemplateHTML( string $value ) : void
	{
		$this->template_html = $value;
	}
	
	public function getTemplateHTML() : string
	{
		return $this->template_html;
	}
	
	public function getTemplateHeader(): string
	{
		return $this->template_header;
	}
	
	public function setTemplateHeader( string $template_header ): void
	{
		$this->template_header = $template_header;
	}
	
	public function getTemplateFooter(): string
	{
		return $this->template_footer;
	}
	
	public function setTemplateFooter( string $template_footer ): void
	{
		$this->template_footer = $template_footer;
	}
	
	
}