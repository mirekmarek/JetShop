<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use JetApplication\EMail_Layout;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\EMail_TemplateText;


#[DataModel_Definition(
	name: 'email_templates_shop_data',
	database_table_name: 'email_templates_shop_data',
	parent_model_class: EMail_TemplateText::class
)]
abstract class Core_EMail_TemplateText_ShopData extends Entity_WithShopData_ShopData
{
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Layout:',
		select_options_creator: [
			EMail_Layout::class,
			'getScope'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Invalid value',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
		
	)]
	protected int $layout_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Sender - e-mail:'
	)]
	protected string $sender_email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Sender - name:'
	)]
	protected string $sender_name = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Subject:'
	)]
	protected string $subject = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Body - HTML:'
	)]
	protected string $body_html = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Body - TXT:'
	)]
	protected string $body_txt = '';
	

	public function getLayoutId(): int
	{
		return $this->layout_id;
	}
	
	public function setLayoutId( int $layout_id ): void
	{
		$this->layout_id = $layout_id;
	}
	
	public function getSenderEmail(): string
	{
		return $this->sender_email;
	}

	public function setSenderEmail( string $sender_email ): void
	{
		$this->sender_email = $sender_email;
	}
	
	public function getSenderName(): string
	{
		return $this->sender_name;
	}
	
	public function setSenderName( string $sender_name ): void
	{
		$this->sender_name = $sender_name;
	}
	
	
	
	public function getSubject(): string
	{
		return $this->subject;
	}
	
	public function setSubject( string $subject ): void
	{
		$this->subject = $subject;
	}
	
	public function setBodyHTML( string $value ) : void
	{
		$this->body_html = $value;
	}
	
	public function getBodyHTML() : string
	{
		return $this->body_html;
	}
	
	public function getBodyTxt(): string
	{
		return $this->body_txt;
	}
	
	public function setBodyTxt( string $body_txt ): void
	{
		$this->body_txt = $body_txt;
	}
	
	
}