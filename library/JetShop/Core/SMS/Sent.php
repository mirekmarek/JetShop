<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\EShopEntity_HasPersonalData_Interface;
use JetApplication\SMS;
use JetApplication\EShopEntity_WithEShopRelation;

#[DataModel_Definition(
	name: 'sent_smss',
	database_table_name: 'sent_smss',
	
)]
abstract class Core_SMS_Sent extends EShopEntity_WithEShopRelation implements EShopEntity_HasPersonalData_Interface
{
	
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
	protected string $to_phone_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999,
	)]
	protected string $text = '';

	
	public static function SMSSent( SMS $sms ) : void
	{
		$item = new static();
		$item->setEshop( $sms->getEshop() );
		$item->template_code = $sms->getTemplateCode();
		$item->context_customer_id = $sms->getContextCustomerId();
		$item->context = $sms->getContext();
		$item->context_id = $sms->getContextId();
		$item->to_phone_number = $sms->getToPhoneNumber();
		$item->text = $sms->getText();
		
		$item->save();
	}
	
	/**
	 * @var string $context
	 * @param int $context_id
	 * @return static[]
	 */
	public static function getByContext( string $context, int $context_id ) : array
	{
		return static::fetch(['sent_smss'=>[
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

	public function getToPhoneNumber(): string
	{
		return $this->to_phone_number;
	}

	public function getText(): string
	{
		return $this->text;
	}
	
	public function deletePersonalData(): void
	{
		$this->to_phone_number = '';
		$this->save();
	}
	
	public static function findAndDeletePersonalData( int $customer_id, string $customer_email, string $customer_phone_number ) : void
	{
		static::updateData(
			data: ['to_phone_number' => ''],
			where: ['to_phone_number'=>$customer_phone_number],
		);
	}
}