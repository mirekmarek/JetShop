<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Data_DateTime;
use Jet\Http_Request;
use Jet\Tr;

use JetApplication\EMailMarketing_Subscribe_Log;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'mailing_subscribe_log',
	database_table_name: 'mailing_subscribe_log',
)]
abstract class Core_EMailMarketing_Subscribe_Log extends EShopEntity_WithEShopRelation
{
	public const EVENT_SUBSCRIBE = 'subscribe';
	public const EVENT_UNSUBSCRIBE = 'unsubscribe';
	public const EVENT_EMAIL_CHANGE = 'email_change';
	

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $email_address = '';

	/**
	 * @var ?Data_DateTime
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $date_time = null;

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $ip_address = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $event = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 999999,
	)]
	protected string $event_data = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $source = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	protected string $comment = '';
	

	/**
	 * @param EShop $eshop
	 * @param string $email_address
	 *
	 * @return EMailMarketing_Subscribe_Log[]
	 */
	public static function get( EShop $eshop, string $email_address ) : iterable
	{
		$where = [
			$eshop->getWhere(),
			'AND',
			'email_address' => $email_address
		];
		
		$list = static::fetchInstances( $where );
		$list->getQuery()->setOrderBy(['-id']);
		
		return $list;
	}

	/**
	 * @return int
	 */
	public function getId() : int
	{
		return $this->id;
	}


	/**
	 * @param string $value
	 */
	public function setEmailAddress( string $value ) : void
	{
		$this->email_address = $value;
	}

	/**
	 * @return string
	 */
	public function getEmailAddress() : string
	{
		return $this->email_address;
	}

	/**
	 * @param string $value
	 */
	public function setIpAddress( string $value ) : void
	{
		$this->ip_address = $value;
	}

	/**
	 * @return string
	 */
	public function getIpAddress() : string
	{
		return $this->ip_address;
	}

	/**
	 * @param Data_DateTime|string|null $value
	 */
	public function setDateTime( Data_DateTime|string|null $value ) : void
	{
		if( $value===null ) {
			$this->date_time = null;
			return;
		}
		
		if( !( $value instanceof Data_DateTime ) ) {
			$value = new Data_DateTime( (string)$value );
		}
		
		$this->date_time = $value;
	}

	/**
	 * @return Data_DateTime|null
	 */
	public function getDateTime() : Data_DateTime|null
	{
		return $this->date_time;
	}

	/**
	 * @param string $value
	 */
	public function setEvent( string $value ) : void
	{
		$this->event = $value;
	}

	/**
	 * @return string
	 */
	public function getEvent() : string
	{
		return $this->event;
	}

	public function getEventTxt() : string
	{
		return match ($this->getEvent()) {
			EMailMarketing_Subscribe_Log::EVENT_SUBSCRIBE => '<span class="badge badge-success">' . Tr::_( 'Subscribe' ) . '</span>',
			EMailMarketing_Subscribe_Log::EVENT_UNSUBSCRIBE => '<span class="badge badge-danger">' . Tr::_( 'Unsubscribe' ) . '</span>',
			EMailMarketing_Subscribe_Log::EVENT_EMAIL_CHANGE => '<span class="badge badge-warning">' . Tr::_( 'Mail change' ) . '</span>',
		};

	}

	/**
	 * @return string
	 */
	public function getEventData(): string
	{
		return $this->event_data;
	}

	/**
	 * @param string $event_data
	 */
	public function setEventData( string $event_data ): void
	{
		$this->event_data = $event_data;
	}



	/**
	 * @param string $value
	 */
	public function setSource( string $value ) : void
	{
		$this->source = $value;
	}

	/**
	 * @return string
	 */
	public function getSource() : string
	{
		return $this->source;
	}

	/**
	 * @param string $value
	 */
	public function setComment( string $value ) : void
	{
		$this->comment = $value;
	}

	/**
	 * @return string
	 */
	public function getComment() : string
	{
		return $this->comment;
	}

	public static function subscribe(
		EShop  $eshop,
		string $email_address,
		string $source,
		string $comment
	) : EMailMarketing_Subscribe_Log {
		return EMailMarketing_Subscribe_Log::_event(
			$eshop,
			$email_address,
			$source,
			$comment,
			EMailMarketing_Subscribe_Log::EVENT_SUBSCRIBE
		);
	}

	public static function unsubscribe(
		EShop  $eshop,
		string $email_address,
		string $source,
		string $comment
	) : EMailMarketing_Subscribe_Log {

		return EMailMarketing_Subscribe_Log::_event(
			$eshop,
			$email_address,
			$source,
			$comment,
			EMailMarketing_Subscribe_Log::EVENT_UNSUBSCRIBE
		);
	}

	
	public static function _event(
		EShop  $eshop,
		string $email_address,
		string $source,
		string $comment,
		string $event,
		string $event_data=''
	) : EMailMarketing_Subscribe_Log {

		$e = new EMailMarketing_Subscribe_Log();
		$e->date_time = Data_DateTime::now();
		$e->ip_address = Http_Request::clientIP();
		$e->event = $event;
		$e->setEshop($eshop);
		$e->email_address = $email_address;
		$e->source = $source;
		$e->comment = $comment;
		$e->event_data = $event_data;

		$e->save();

		return $e;
	}


	public static function changeMail( EShop $eshop, string $old_email_address, string $new_mail_address, string $source, string $comment='' ) : void
	{
		EMailMarketing_Subscribe_Log::updateData(
			[
				'email_address' => $new_mail_address
			],
			[
				$eshop->getWhere(),
				'AND',
				'email_address' => $old_email_address
			]
		);

		EMailMarketing_Subscribe_Log::_event(
			$eshop,
			$new_mail_address,
			$source,
			$comment,
			EMailMarketing_Subscribe_Log::EVENT_EMAIL_CHANGE,
			$old_email_address.' > '.$new_mail_address
		);
	}
}
