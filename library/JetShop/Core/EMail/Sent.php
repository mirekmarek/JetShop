<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\EMail;
use JetApplication\Entity_WithEShopRelation;

#[DataModel_Definition(
	name: 'sent_emails',
	database_table_name: 'sent_emails',
	
)]
abstract class Core_EMail_Sent extends Entity_WithEShopRelation {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $template_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $context_customer_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $context = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $context_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $to = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $to_copy = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $to_hidden_copy = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $sender_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $sender_email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	protected string $attachments = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $subject = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999,
	)]
	protected string $body_html = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999,
	)]
	protected string $body_txt = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999,
	)]
	protected string $custom_headers = '';
	
	public static function emailSent( EMail $email ) : void
	{
		$item = new static();
		$item->setEshop( $email->getEshop() );
		$item->template_code = $email->getTemplateCode();
		$item->context_customer_id = $email->getContextCustomerId();
		$item->context = $email->getContext();
		$item->context_id = $email->getContextId();
		$item->to = is_array($email->getTo()) ? implode("\n", $email->getTo()) : $email->getTo();
		$item->to_copy = is_array($email->getToCopy()) ? implode("\n", $email->getToCopy()) : $email->getToCopy();
		$item->to_hidden_copy = is_array($email->getToHiddenCopy()) ? implode("\n", $email->getToHiddenCopy()) : $email->getToHiddenCopy();
		$item->sender_name = $email->getSenderName();
		$item->sender_email = $email->getSenderEmail();
		$item->attachments = implode("\n", $email->getAttachments());
		$item->subject = $email->getSubject();
		$item->body_txt = $email->getBodyTxt();
		$item->body_html = $email->getBodyHtmlRaw();
		$item->custom_headers = implode("\n", $email->getCustomHeaders());
		
		$item->save();
	}
	
	/**
	 * @var string $context
	 * @param int $context_id
	 * @return static[]
	 */
	public static function getByContext( string $context, int $context_id ) : array
	{
		return static::fetch(['sent_emails'=>[
			'context' => $context,
			'AND',
			'context_id' => $context_id
		]],
		order_by: ['-id']);
	}
	

	public function getTemplateCode(): string
	{
		return $this->template_code;
	}
	
	public function getContextCustomerId(): int
	{
		return $this->context_customer_id;
	}

	public function getContext(): string
	{
		return $this->context;
	}
	
	

	public function getContextId(): int
	{
		return $this->context_id;
	}

	public function getTo(): string
	{
		return $this->to;
	}

	public function getToCopy(): string
	{
		return $this->to_copy;
	}

	public function getToHiddenCopy(): string
	{
		return $this->to_hidden_copy;
	}

	public function getSenderName(): string
	{
		return $this->sender_name;
	}
	
	public function getSenderEmail(): string
	{
		return $this->sender_email;
	}

	public function getAttachments(): string
	{
		return $this->attachments;
	}
	
	public function getSubject(): string
	{
		return $this->subject;
	}
	
	public function getBodyHtml(): string
	{
		return $this->body_html;
	}

	public function getBodyTxt(): string
	{
		return $this->body_txt;
	}
	
	public function getCustomHeaders(): string
	{
		return $this->custom_headers;
	}
	
	
	
}