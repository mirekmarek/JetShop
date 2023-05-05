<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Data_DateTime;
use Jet\Http_Request;
use Jet\Tr;

use JetApplication\CommonEntity_ShopRelationTrait;
use JetApplication\Customer_MailingSubscribe_Log;
use JetApplication\Shops_Shop;

/**
 *
 */
#[DataModel_Definition(
	name: 'customer_mailing_subscribe_log',
	database_table_name: 'customers_mailing_subscribe_log',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_Customer_MailingSubscribe_Log extends DataModel
{
	const EVENT_SUBSCRIBE = 'subscribe';
	const EVENT_UNSUBSCRIBE = 'unsubscribe';
	const EVENT_JOIN_MAIL_TO_CUSTOMER = 'join_mail_to_customer';
	const EVENT_EMAIL_CHANGE = 'email_change';


	use CommonEntity_ShopRelationTrait;

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
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $customer_id = 0;

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
	 * @param int $id
	 * @return static|null
	 */
	public static function get( int $id ) : static|null
	{
		return static::load( $id );
	}

	/**
	 * @param Shops_Shop $shop
	 * @param string $email_address
	 *
	 * @return Customer_MailingSubscribe_Log[]
	 */
	public static function getList( Shops_Shop $shop, string $email_address ) : iterable
	{
		$where = [
			$shop->getWhere(),
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
	 * @param int $value
	 */
	public function setCustomerId( int $value ) : void
	{
		$this->customer_id = $value;
	}

	/**
	 * @return int
	 */
	public function getCustomerId() : int
	{
		return $this->customer_id;
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
			Customer_MailingSubscribe_Log::EVENT_SUBSCRIBE => '<span class="badge badge-success">' . Tr::_( 'Subscribe' ) . '</span>',
			Customer_MailingSubscribe_Log::EVENT_UNSUBSCRIBE => '<span class="badge badge-danger">' . Tr::_( 'Unsubscribe' ) . '</span>',
			Customer_MailingSubscribe_Log::EVENT_JOIN_MAIL_TO_CUSTOMER => '<span class="badge badge-info">' . Tr::_( 'Joined to customer' ) . '</span>',
			Customer_MailingSubscribe_Log::EVENT_EMAIL_CHANGE => '<span class="badge badge-warning">' . Tr::_( 'Mail change' ) . '</span>',
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
		Shops_Shop $shop,
		string $email_address,
		int $customer_id,
		string $source,
		string $comment
	) : Customer_MailingSubscribe_Log {
		return Customer_MailingSubscribe_Log::_event(
			$shop,
			$email_address,
			$customer_id,
			$source,
			$comment,
			Customer_MailingSubscribe_Log::EVENT_SUBSCRIBE
		);
	}

	public static function unsubscribe(
		Shops_Shop $shop,
		string $email_address,
		int $customer_id,
		string $source,
		string $comment
	) : Customer_MailingSubscribe_Log {

		return Customer_MailingSubscribe_Log::_event(
			$shop,
			$email_address,
			$customer_id,
			$source,
			$comment,
			Customer_MailingSubscribe_Log::EVENT_UNSUBSCRIBE
		);
	}

	public static function joinMailToCustomer(
		Shops_Shop $shop,
		string $email_address,
		int $customer_id,
		string $source,
		string $comment
	) : Customer_MailingSubscribe_Log {
		return Customer_MailingSubscribe_Log::_event(
			$shop,
			$email_address,
			$customer_id,
			$source,
			$comment,
			Customer_MailingSubscribe_Log::EVENT_JOIN_MAIL_TO_CUSTOMER
		);
	}


	public static function _event(
		Shops_Shop $shop,
		string $email_address,
		int $customer_id,
		string $source,
		string $comment,
		string $event,
		string $event_data=''
	) : Customer_MailingSubscribe_Log {

		$e = new Customer_MailingSubscribe_Log();
		$e->date_time = Data_DateTime::now();
		$e->ip_address = Http_Request::clientIP();
		$e->event = $event;
		$e->setShop($shop);
		$e->email_address = $email_address;
		$e->customer_id = $customer_id;
		$e->source = $source;
		$e->comment = $comment;
		$e->event_data = $event_data;

		$e->save();

		return $e;
	}


	public static function changeMail( Shops_Shop $shop,string $old_email_address, string $new_mail_address, int $customer_id, string $source, string $comment='' ) : void
	{
		Customer_MailingSubscribe_Log::updateData(
			[
				'email_address' => $new_mail_address
			],
			[
				$shop->getWhere(),
				'AND',
				'email_address' => $old_email_address
			]
		);

		Customer_MailingSubscribe_Log::_event(
			$shop,
			$new_mail_address,
			$customer_id,
			$source,
			$comment,
			Customer_MailingSubscribe_Log::EVENT_EMAIL_CHANGE,
			$old_email_address.' > '.$new_mail_address
		);
	}
}
