<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops_Shop;
use JetApplication\Customer_MailingSubscribe;
use JetApplication\Customer_MailingSubscribe_Log;

/**
 *
 */
#[DataModel_Definition(
	name: 'customer_mailing_subscribe',
	database_table_name: 'customers_mailing_subscribe',
)]
abstract class Core_Customer_MailingSubscribe extends Entity_WithShopRelation
{

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		is_id: true,
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



	public static function get( Shops_Shop $shop, string $email_address ) : static|null
	{
		return static::load( [
			$shop->getWhere(),
			'AND',
			'email_address' => $email_address
		] );
	}



	public static function getList( Shops_Shop $shop ) : iterable
	{
		$where = $shop->getWhere();
		
		$list = static::fetchInstances( $where );
		
		return $list;
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

	public static function subscribe( Shops_Shop $shop, string $email_address, string $source, int $customer_id=0, string $comment='' ) : void
	{
		$exists_reg = Customer_MailingSubscribe::get( $shop, $email_address );
		if($exists_reg) {
			if($customer_id && !$exists_reg->customer_id) {
				Customer_MailingSubscribe::joinMailToCustomer(
					$shop,
					$email_address,
					$customer_id,
					$source,
					$comment
				);
			}

			return;
		}

		$reg = new Customer_MailingSubscribe();
		$reg->setShop($shop);
		$reg->email_address = $email_address;
		$reg->customer_id = $customer_id;

		$reg->save();

		Customer_MailingSubscribe_Log::subscribe(
			$shop,
			$email_address,
			$customer_id,
			$source,
			$comment
		);
	}

	public static function unsubscribe( Shops_Shop $shop, string $email_address, string $source, int $customer_id=0, string $comment='' ) : void
	{
		$exists_reg = Customer_MailingSubscribe::get( $shop, $email_address );
		if(!$exists_reg) {
			return;
		}

		$exists_reg->delete();

		Customer_MailingSubscribe_Log::unsubscribe(
			$shop,
			$email_address,
			$customer_id,
			$source,
			$comment
		);
	}

	public static function joinMailToCustomer(  Shops_Shop $shop, string $email_address, int $customer_id, string $source, string $comment='' ) : void
	{
		$exists_reg = Customer_MailingSubscribe::get( $shop, $email_address );
		if(!$exists_reg) {
			return;
		}

		if($exists_reg->customer_id==$customer_id) {
			return;
		}

		$exists_reg->customer_id = $customer_id;
		$exists_reg->save();

		Customer_MailingSubscribe_Log::joinMailToCustomer(
			$shop,
			$email_address,
			$customer_id,
			$source,
			$comment
		);

	}

	public static function changeMail( Shops_Shop $shop,string $old_email_address, string $new_mail_address, string $source, string $comment='' ) : void
	{

		$exists_reg = Customer_MailingSubscribe::get( $shop, $old_email_address );
		$customer_id = 0;
		if($exists_reg) {
			$customer_id = $exists_reg->customer_id;

			Customer_MailingSubscribe::updateData(
				[
					'email_address' => $new_mail_address
				],
				[
					'email_address' => $old_email_address,
					'AND',
					$shop->getWhere()
				]
			);
		}

		Customer_MailingSubscribe_Log::changeMail(
			$shop,
			$old_email_address,
			$new_mail_address,
			$customer_id,
			$source,
			$comment
		);

	}
}
